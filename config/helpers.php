<?php
if (!function_exists('textoConEnlaces')) {
    function _textoEnlacesCallback($m) {
        return '<a href="' . $m[1] . '" target="_blank" rel="noopener noreferrer">' . $m[2] . '</a>';
    }
    function textoConEnlaces($texto) {
        if ($texto === null || $texto === false) return '';
        $safe = htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
        $safe = preg_replace_callback(
            '/&lt;a\s+href=&quot;(https?:\/\/[^&"<>\s]+)&quot;&gt;(.*?)&lt;\/a&gt;/is',
            '_textoEnlacesCallback',
            $safe
        );
        return $safe === null ? htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8') : $safe;
    }
}
?>
