<?php

namespace App\Support\PhotoCheck;

// File checks that need no internet: duplicates, size, EXIF date/GPS, screenshot
// and editor hints. Uses only PHP's GD and EXIF extensions, no Laravel, so it
// can be tested on its own. It only reports flags; it never rejects a photo.
class BasicPhotoChecker
{
    // Editors that write their name into EXIF "Software". Phone camera apps also
    // write this tag, so only real editing apps are listed.
    private const EDITORS = ['photoshop', 'lightroom', 'gimp', 'snapseed', 'picsart',
        'canva', 'pixlr', 'facetune', 'meitu', 'photoroom', 'remini', 'affinity'];

    public function __construct(private array $config)
    {
    }

    /**
     * @param string        $path        Full path of the saved photo
     * @param array         $report      ['latitude' => ?float, 'longitude' => ?float, 'reported_at' => \DateTimeInterface]
     * @param callable|null $findMatches fn(string $sha256, ?string $dhash): array of
     *                                   ['tracking_code' => ..., 'photo_sha256' => ..., 'photo_dhash' => ...]
     *                                   for earlier concerns (exclude this one)
     * @return array{sha256: string, dhash: ?string, flags: array}
     */
    public function check(string $path, array $report, ?callable $findMatches = null): array
    {
        $flags = [];
        $sha256 = hash_file('sha256', $path);
        $bytes = filesize($path);
        $info = @getimagesize($path);
        [$width, $height] = $info ? [$info[0], $info[1]] : [0, 0];
        $mime = $info['mime'] ?? '';
        $exif = $mime === 'image/jpeg' && function_exists('exif_read_data')
            ? (@exif_read_data($path) ?: [])
            : [];

        $dhash = self::dhash($path);

        // Duplicates of earlier reports
        if ($findMatches) {
            $nearLimit = (int) ($this->config['near_duplicate_distance'] ?? 8);
            $exactCodes = [];
            $nearCodes = [];
            foreach ($findMatches($sha256, $dhash) as $row) {
                if (($row['photo_sha256'] ?? null) === $sha256) {
                    $exactCodes[] = $row['tracking_code'];
                } elseif ($dhash && ! empty($row['photo_dhash'])
                    && self::hamming($dhash, $row['photo_dhash']) <= $nearLimit) {
                    $nearCodes[] = $row['tracking_code'];
                }
            }
            if ($exactCodes) {
                $flags[] = self::flag('duplicate', 'Same photo as an earlier report', 'strong', $exactCodes);
            }
            if ($nearCodes) {
                $flags[] = self::flag('near_duplicate', 'Very similar to a photo in an earlier report (maybe cropped or re-saved)', 'medium', $nearCodes);
            }
        }

        // Too small to be a real photo of the problem
        $minDim = (int) ($this->config['min_dimension'] ?? 300);
        $minBytes = (int) ($this->config['min_bytes'] ?? 15360);
        if ($width && $height && min($width, $height) < $minDim) {
            $flags[] = self::flag('too_small', "Very small image ({$width}×{$height} px)", 'medium');
        } elseif ($bytes < $minBytes) {
            $flags[] = self::flag('too_small', 'Very small file (' . round($bytes / 1024) . ' KB)', 'medium');
        }

        // EXIF date
        $taken = self::exifDate($exif, $report['reported_at']->getTimezone());
        if ($taken) {
            $ageDays = ($report['reported_at']->getTimestamp() - $taken->getTimestamp()) / 86400;
            $maxAge = (int) ($this->config['max_photo_age_days'] ?? 30);
            if ($ageDays > $maxAge) {
                $flags[] = self::flag('old_photo', 'Photo was taken ' . self::ago($ageDays) . ' before the report (' . $taken->format('M j, Y') . ')', 'medium');
            } elseif ($ageDays < -1) {
                $flags[] = self::flag('future_date', 'Photo date is after the report date (camera clock may be wrong)', 'weak');
            }
        }

        // EXIF GPS
        $gps = self::exifGps($exif);
        if ($gps) {
            if (isset($report['latitude'], $report['longitude'])) {
                $km = self::distanceKm($gps[0], $gps[1], (float) $report['latitude'], (float) $report['longitude']);
                if ($km > (float) ($this->config['max_km_from_pin'] ?? 1)) {
                    $flags[] = self::flag('far_from_pin', 'Photo location is ' . self::km($km) . ' from the map pin', 'medium');
                }
            } else {
                $km = self::distanceKm($gps[0], $gps[1], (float) $this->config['center_lat'], (float) $this->config['center_lng']);
                if ($km > (float) ($this->config['max_km_from_center'] ?? 3)) {
                    $flags[] = self::flag('outside_barangay', 'Photo location is ' . self::km($km) . ' from the barangay center', 'medium');
                }
            }
        }

        // Screenshot hint: phone-screen shape and no camera data
        if ($width && $height) {
            $ratio = max($width, $height) / min($width, $height);
            $noCamera = empty($exif['Make']) && empty($exif['Model']);
            if ($noCamera && $ratio >= 1.75 && $ratio <= 2.4 && ($mime === 'image/png' || $mime === 'image/webp')) {
                $flags[] = self::flag('screenshot', 'May be a screenshot (phone-screen size, no camera data)', 'weak');
            }
        }

        // AI-generated image markers in the file itself
        foreach (self::aiMarkers($path, $mime, $exif, $width, $height) as $f) {
            $flags[] = $f;
        }

        // Editing app
        $software = strtolower((string) ($exif['Software'] ?? ''));
        foreach (self::EDITORS as $editor) {
            if ($software !== '' && str_contains($software, $editor)) {
                $flags[] = self::flag('edited', 'Saved by an editing app (' . trim($exif['Software']) . ')', 'weak');
                break;
            }
        }

        return ['sha256' => $sha256, 'dhash' => $dhash, 'flags' => $flags];
    }

    // AI image tools whose name can appear in a file's metadata or Content Credentials.
    private const AI_TOOLS = ['Midjourney', 'DALL-E', 'DALL·E', 'DALLE', 'OpenAI', 'ChatGPT', 'Stable Diffusion',
        'stable-diffusion', 'StableDiffusion', 'Adobe Firefly', 'Google Imagen', 'Gemini', 'Leonardo.Ai', 'NovelAI',
        'ComfyUI', 'InvokeAI', 'Automatic1111', 'DreamStudio', 'Ideogram', 'Bing Image Creator', 'Microsoft Designer',
        'Meta AI', 'Grok Imagine', 'Kandinsky', 'Playground AI', 'NightCafe', 'Craiyon', 'starryai', 'Runway'];

    // Output sizes common to AI image tools (real phone photos are almost never these sizes).
    private const AI_SIZES = ['512x512', '768x768', '1024x1024', '1536x1536', '2048x2048', '1024x1536', '1536x1024',
        '1024x1792', '1792x1024', '832x1216', '1216x832', '896x1152', '1152x896', '768x1344', '1344x768',
        '640x1536', '1536x640', '1152x2048', '2048x1152', '1408x768', '768x1408', '1664x928', '928x1664'];

    /**
     * Signs inside the file that it was made by an AI image tool.
     * Labels are trustworthy when present, but screenshots, Messenger and re-saving
     * usually strip them, so a missing label proves nothing.
     */
    public static function aiMarkers(string $path, string $mime, array $exif, int $width, int $height): array
    {
        $flags = [];
        $data = (string) @file_get_contents($path);

        // IPTC "digital source type" label (XMP or Content Credentials). Case-sensitive on
        // purpose: "trainedAlgorithmicMedia" is fully AI-made, "compositeWith..." is AI-edited.
        if (str_contains($data, 'digitalsourcetype/trainedAlgorithmicMedia') || str_contains($data, '"trainedAlgorithmicMedia"')
            || preg_match('/DigitalSourceType[^<>]{0,80}[\/:"\'>]trainedAlgorithmicMedia/', $data)) {
            $flags[] = self::flag('ai_label', 'File is labeled as AI-generated', 'strong');
        } elseif (str_contains($data, 'compositeWithTrainedAlgorithmicMedia')) {
            $flags[] = self::flag('ai_label_edit', 'File is labeled as edited with AI (e.g. generative fill)', 'medium');
        }

        // Settings that Stable Diffusion tools save inside PNG files
        if ($mime === 'image/png') {
            $text = self::pngText($data);
            $a1111 = isset($text['parameters']) && preg_match('/Steps:\s*\d+.*Sampler:/s', $text['parameters']);
            $comfy = isset($text['workflow']) || (isset($text['prompt']) && str_contains($text['prompt'], 'class_type'));
            if ($a1111 || $comfy || isset($text['sd-metadata']) || isset($text['invokeai_metadata'])) {
                $flags[] = self::flag('ai_settings', 'File contains AI image generator settings (prompt, steps, sampler)', 'strong');
            }
        }

        // An AI tool named in the metadata or Content Credentials (C2PA)
        $already = (bool) $flags;
        if (! $already) {
            $where = implode(' ', array_map('strval', array_filter([
                $exif['Software'] ?? null, $exif['Artist'] ?? null, $exif['Make'] ?? null, $exif['Model'] ?? null,
                $exif['ImageDescription'] ?? null,
            ], 'is_scalar')));
            $hasC2pa = str_contains($data, 'c2pa') && (str_contains($data, 'jumb') || str_contains($data, 'caBX'));
            $xmp = self::xmp($data);
            foreach (self::AI_TOOLS as $tool) {
                if (stripos($where, $tool) !== false || stripos($xmp, $tool) !== false
                    || ($hasC2pa && strpos($data, $tool) !== false)) {
                    $flags[] = self::flag('ai_tool', "Metadata names an AI image tool ({$tool})", 'strong');
                    break;
                }
            }
            if (! $flags && $hasC2pa) {
                $flags[] = self::flag('content_credentials', 'Has Content Credentials (C2PA); no AI label found', 'info');
            }
        }

        // Typical AI output size and no camera data: only a hint
        $noCamera = empty($exif['Make']) && empty($exif['Model']);
        if (! $flags && $noCamera && in_array("{$width}x{$height}", self::AI_SIZES, true)) {
            $flags[] = self::flag('ai_size', "Size matches AI image tools ({$width}×{$height}) and has no camera data", 'weak');
        }

        return $flags;
    }

    // tEXt / iTXt / zTXt keywords and values of a PNG file.
    private static function pngText(string $data): array
    {
        $out = [];
        if (substr($data, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return $out;
        }
        $pos = 8;
        $len = strlen($data);
        while ($pos + 12 <= $len) {
            $size = unpack('N', substr($data, $pos, 4))[1];
            $type = substr($data, $pos + 4, 4);
            if ($size > $len - $pos - 12) {
                break;
            }
            $body = substr($data, $pos + 8, $size);
            if ($type === 'tEXt' || $type === 'zTXt' || $type === 'iTXt') {
                [$key, $rest] = array_pad(explode("\0", $body, 2), 2, '');
                if ($type === 'zTXt') {
                    $rest = (string) @gzuncompress(substr($rest, 1));
                } elseif ($type === 'iTXt') {
                    // flag, method, language\0, translated keyword\0, text
                    $compressed = ($rest[0] ?? "\0") === "\1";
                    $parts = explode("\0", substr($rest, 2), 3);
                    $rest = $parts[2] ?? '';
                    if ($compressed) {
                        $rest = (string) @gzuncompress($rest);
                    }
                }
                $out[$key] = mb_substr($rest, 0, 20000);
            }
            if ($type === 'IEND') {
                break;
            }
            $pos += 12 + $size;
        }

        return $out;
    }

    // XMP packet text (where tools write CreatorTool and similar), or ''.
    private static function xmp(string $data): string
    {
        $start = strpos($data, '<x:xmpmeta');
        if ($start === false) {
            return '';
        }
        $end = strpos($data, '</x:xmpmeta>', $start);

        return substr($data, $start, $end === false ? 20000 : min(200000, $end - $start + 12));
    }

    // Flag codes that mean "possibly AI-generated" (admin badge and filter use these).
    public const AI_CODES = ['ai_label', 'ai_label_edit', 'ai_settings', 'ai_tool', 'ai_size', 'ai_generated_visual'];

    // Flagged when any strong/medium flag, or two or more weak ones.
    // A single weak flag is kept as a note but the photo stays "clear".
    public static function statusFor(array $flags): string
    {
        $weak = 0;
        foreach ($flags as $f) {
            if (in_array($f['severity'], ['strong', 'medium'], true)) {
                return 'flagged';
            }
            if ($f['severity'] === 'weak') {
                $weak++;
            }
        }

        return $weak >= 2 ? 'flagged' : 'clear';
    }

    public static function flag(string $code, string $label, string $severity, array $related = [], string $source = 'basic'): array
    {
        $flag = compact('code', 'label', 'severity', 'source');
        if ($related) {
            $flag['related'] = array_values(array_unique($related));
        }

        return $flag;
    }

    // 64-bit difference hash as 16 hex chars, or null if GD cannot read the image.
    public static function dhash(string $path): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }
        $data = @file_get_contents($path);
        $src = $data !== false ? @imagecreatefromstring($data) : false;
        if (! $src) {
            return null;
        }
        $small = imagecreatetruecolor(9, 8);
        imagecopyresampled($small, $src, 0, 0, 0, 0, 9, 8, imagesx($src), imagesy($src));
        imagedestroy($src);

        $bits = '';
        for ($y = 0; $y < 8; $y++) {
            $prev = null;
            for ($x = 0; $x < 9; $x++) {
                $rgb = imagecolorat($small, $x, $y);
                $gray = (($rgb >> 16) & 0xFF) * 299 + (($rgb >> 8) & 0xFF) * 587 + ($rgb & 0xFF) * 114;
                if ($prev !== null) {
                    $bits .= $gray > $prev ? '1' : '0';
                }
                $prev = $gray;
            }
        }
        imagedestroy($small);

        $hex = '';
        foreach (str_split($bits, 4) as $nibble) {
            $hex .= dechex(bindec($nibble));
        }

        return $hex;
    }

    public static function hamming(string $a, string $b): int
    {
        $d = 0;
        // 8 hex chars at a time keeps the numbers inside a PHP int on any platform
        foreach ([0, 8] as $i) {
            $x = hexdec(substr($a, $i, 8)) ^ hexdec(substr($b, $i, 8));
            $d += substr_count(decbin($x), '1');
        }

        return $d;
    }

    public static function exifDate(array $exif, \DateTimeZone $tz): ?\DateTimeImmutable
    {
        foreach (['DateTimeOriginal', 'DateTimeDigitized', 'DateTime'] as $key) {
            $raw = trim((string) ($exif[$key] ?? ''));
            if ($raw === '' || str_starts_with($raw, '0000')) {
                continue;
            }
            $date = \DateTimeImmutable::createFromFormat('Y:m:d H:i:s', $raw, $tz);
            if ($date && (int) $date->format('Y') >= 1990) {
                return $date;
            }
        }

        return null;
    }

    /** @return array{0: float, 1: float}|null */
    public static function exifGps(array $exif): ?array
    {
        if (empty($exif['GPSLatitude']) || empty($exif['GPSLongitude'])) {
            return null;
        }
        $lat = self::dms($exif['GPSLatitude']);
        $lng = self::dms($exif['GPSLongitude']);
        if ($lat === null || $lng === null || ($lat == 0.0 && $lng == 0.0)) {
            return null;
        }
        if (strtoupper((string) ($exif['GPSLatitudeRef'] ?? 'N')) === 'S') {
            $lat = -$lat;
        }
        if (strtoupper((string) ($exif['GPSLongitudeRef'] ?? 'E')) === 'W') {
            $lng = -$lng;
        }

        return abs($lat) <= 90 && abs($lng) <= 180 ? [$lat, $lng] : null;
    }

    private static function dms($parts): ?float
    {
        if (! is_array($parts) || count($parts) < 3) {
            return null;
        }
        $v = [];
        foreach (array_slice(array_values($parts), 0, 3) as $p) {
            $p = (string) $p;
            if (str_contains($p, '/')) {
                [$n, $d] = explode('/', $p, 2);
                if ((float) $d == 0.0) {
                    return null;
                }
                $v[] = (float) $n / (float) $d;
            } else {
                $v[] = (float) $p;
            }
        }

        return $v[0] + $v[1] / 60 + $v[2] / 3600;
    }

    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $r * asin(min(1, sqrt($a)));
    }

    private static function km(float $km): string
    {
        return $km < 10 ? number_format($km, 1) . ' km' : number_format($km) . ' km';
    }

    private static function ago(float $days): string
    {
        if ($days >= 730) {
            return floor($days / 365) . ' years';
        }
        if ($days >= 60) {
            return floor($days / 30) . ' months';
        }

        return floor($days) . ' days';
    }
}
