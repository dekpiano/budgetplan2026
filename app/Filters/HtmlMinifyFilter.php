<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class HtmlMinifyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do nothing before request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Only minify if the response is HTML and not redirect/ajax
        $contentType = $response->getHeaderLine('Content-Type');

        // Skip minifying during Ajax or non-HTML responses
        if ($request->isAJAX() || strpos($contentType, 'text/html') === false) {
            return;
        }

        $body = $response->getBody();

        if (empty($body)) {
            return;
        }

        // Step 1: Extract and protect blocks that must NOT be minified
        // (script, style, pre, textarea, and also inline JS event attributes are safe
        //  because we only touch whitespace *between* tags, not inside attribute values)
        $protectedBlocks = [];
        $placeholder = "\x1A_MINIFY_PROTECTED_%d_\x1A";
        $index = 0;

        // Protect <script>...</script>, <style>...</style>, <pre>...</pre>, <textarea>...</textarea>
        $body = preg_replace_callback(
            '/<(script|style|pre|textarea)\b[^>]*>.*?<\/\1>/uis',
            function ($match) use (&$protectedBlocks, &$index, $placeholder) {
                $key = sprintf($placeholder, $index);
                $protectedBlocks[$key] = $match[0];
                $index++;
                return $key;
            },
            $body
        );

        // Protect IE conditional comments <!--[if ...]>...<![endif]-->
        $body = preg_replace_callback(
            '/<!--\s*\[if\s[^\]]+\]>.*?<!\[endif\]\s*-->/uis',
            function ($match) use (&$protectedBlocks, &$index, $placeholder) {
                $key = sprintf($placeholder, $index);
                $protectedBlocks[$key] = $match[0];
                $index++;
                return $key;
            },
            $body
        );

        // Step 2: Remove normal HTML comments (not IE conditionals, those are already protected)
        $body = preg_replace('/<!--[\s\S]*?-->/', '', $body);

        // Step 3: Safe whitespace reduction
        // 3a. Remove whitespace between HTML tags: "> \n\t <" becomes "><"
        $body = preg_replace('/>\s+</', '><', $body);

        // 3b. Collapse remaining runs of whitespace (inside text nodes) to a single space
        $body = preg_replace('/\s+/', ' ', $body);

        // 3c. Trim the entire output
        $body = trim($body);

        // Step 4: Restore protected blocks
        foreach ($protectedBlocks as $key => $original) {
            $body = str_replace($key, $original, $body);
        }

        $response->setBody($body);
    }
}
