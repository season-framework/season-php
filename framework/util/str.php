<?php

class str
{

    const FNM_PATHNAME = 1;
    const FNM_NOESCAPE = 2;
    const FNM_PERIOD = 4;
    const FNM_CASEFOLD = 16;

    function startswith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    function endswith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public function match($pattern, $string, $flags = 0)
    {
        return $this->pcre_fnmatch($pattern, $string, $flags);
    }

    private function pcre_fnmatch($pattern, $string, $flags = 0)
    {
        $modifiers = null;
        $transforms = array(
            '\*'    => '.*',
            '\?'    => '.',
            '\[\!'    => '[^',
            '\['    => '[',
            '\]'    => ']',
            '\-'    => '-',
            '\.'    => '\.',
            '\\'    => '\\\\'
        );

        if ($flags & self::FNM_PATHNAME) {
            $transforms['\*'] = '[^/]*';
        }

        if ($flags & self::FNM_NOESCAPE) {
            unset($transforms['\\']);
        }

        if ($flags & self::FNM_CASEFOLD) {
            $modifiers .= 'i';
        }

        if ($flags & self::FNM_PERIOD) {
            if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0) return false;
        }

        $pattern = '#^'
            . strtr(preg_quote($pattern, '#'), $transforms)
            . '$#'
            . $modifiers;
        return (bool) preg_match($pattern, $string);
    }
}
