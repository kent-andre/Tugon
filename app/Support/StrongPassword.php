<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

// One set of password rules for Register, Reset Password and Settings > Change Password.
// The page shows the same rules live (resources/views/partials/password-strength.blade.php),
// but the server always checks again here.
class StrongPassword
{
    // $personal: the person's name parts and email. The password may not contain them.
    public static function rules(array $personal = []): array
    {
        $tokens = self::tokens($personal);

        return [
            'required',
            'string',
            'max:100',
            'confirmed',
            Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            function (string $attribute, $value, $fail) use ($tokens) {
                $lower = mb_strtolower((string) $value);
                foreach ($tokens as $t) {
                    if (str_contains($lower, $t)) {
                        $fail('Your password must not contain your name or email.');

                        return;
                    }
                }
            },
        ];
    }

    public static function messages(): array
    {
        return [
            'password.confirmed' => 'The two passwords do not match.',
            'password.min'       => 'Use at least 8 characters.',
        ];
    }

    // "Kent Andre", "kentandreaguilar@gmail.com" -> ["kent", "andre", "kentandreaguilar"]
    // Parts shorter than 3 letters are ignored so short names don't block everything.
    public static function tokens(array $personal): array
    {
        $out = [];
        foreach ($personal as $value) {
            $value = mb_strtolower(trim((string) $value));
            if ($value === '') {
                continue;
            }
            if (str_contains($value, '@')) {
                $value = strstr($value, '@', true);
            }
            foreach (preg_split('/[^\p{L}\p{N}]+/u', $value) as $part) {
                if (mb_strlen($part) >= 3) {
                    $out[] = $part;
                }
            }
        }

        return array_values(array_unique($out));
    }
}
