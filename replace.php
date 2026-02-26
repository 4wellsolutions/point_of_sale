<?php

// Script to standardize decimal places in views

$dir = new RecursiveDirectoryIterator('d:/projects/pos/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$count = 0;

foreach ($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;

        // 1. Replace number_format() calls with format_number()
        // Match number_format($variable), number_format($var, 2), number_format($var, 2, '.', '') etc.
        // We capture the first argument. Since arguments can have nested parenthesis like number_format(abs($val), 2),
        // we use a regex that handles up to one level of nested parenthesis.
        // \((?:[^)(]+|\([^)(]*\))*\) -> matches (...)

        $content = preg_replace_callback('/number_format\s*\(\s*((?:[^)(]+|\([^)(]*\))*)\s*(?:,.*?)?\)/', function ($matches) {
            $arg = trim($matches[1]);
            return "format_number({$arg})";
        }, $content);

        // 2. Replace .toFixed(2) in JS blocks to parseFloat(val.toFixed(2)) to strip zeros
        // But let's verify how it's used. Typically `val.toFixed(2)`.
        // We regex match `.toFixed(2)` and wrap the whole preceding expression? That's hard in Regex.
        // Let's replace `.toFixed(2)` with `.toFixed(2).replace(/\.?0+$/, "")` -> Wait, what about "100.00" -> "100"?
        // Better: replace `(.toFixed(2))` with `((...)? ... : ...)`? No.
        // If we just replace `.toFixed(2)` with something else.
        // Actually, JavaScript `Number(num.toFixed(2))` works perfectly.
        // Or `+num.toFixed(2)` (unary plus) which strips trailing zeros.
        // "Number(total.toFixed(2))" or "parseFloat(total.toFixed(2))"
        // Let's replace `.toFixed(2)` with `.toFixed(2).replace(/\.00$/, '').replace(/(\.[0-9])0$/, '$1')`
        // Or `parseFloat(`.toFixed(2) -> wait, syntax error if we don't wrap the left side.
        // Best approach for JS: We can leave .toFixed(2) as is and add unary plus if possible, BUT regex for left side is hard (`row.find('.sale_price').val(parseFloat(salePrice).toFixed(2));`).
        // If it's `.val( parseFloat(salePrice).toFixed(2) )`, replacing `.toFixed(2)` with empty would break rounding.
        // We want rounding to 2 decimals, then strip.
        // Let's replace `.toFixed(2)` with `?`... Actually, if we define a global JS function `formatNumber()` in layout...
        // Let's replace `\.toFixed\(2\)` with `.toFixed(2).replace(/\.?0+$/, '')` — this drops ".00" and trailing zeroes (e.g. ".50" -> ".5", "100.00" -> "100").
        // What if it's "100.00"? `/\.?0+$/` would match ".00".
        // Wait, "100" -> ".00" is stripped. What about "1000"? `\.?` is optional, so it might strip "0" from "1000"!
        // Better regex: `replace(/\.00$/, '').replace(/(\.\d)0$/, '$1')`
        // Let's try: `.toFixed(2).replace(/\.00$/, '').replace(/(\.[0-9])0$/, '$1')`
        // Wait, what if it's `.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1')` -> yes!

        $content = str_replace('.toFixed(2)', '.toFixed(2).replace(/\.00$/, \'\').replace(/(\.\d)0$/, \'$1\')', $content);

        // 3. Replace step="0.01" to step="any" where applicable, to allow clean integer inputs without browser validation errors? 
        // HTML input step="0.01" already allows "100" as well as "100.5". So step="0.01" is fine.

        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            $count++;
        }
    }
}
echo "Updated $count Blade files.\n";

// Also check controllers/models just in case they return number_format directly?
$dirs = ['d:/projects/pos/app/Http/Controllers', 'd:/projects/pos/app/Models'];
foreach ($dirs as $dirPath) {
    if (!is_dir($dirPath))
        continue;
    $dir = new RecursiveDirectoryIterator($dirPath);
    $ite = new RecursiveIteratorIterator($dir);
    foreach ($ite as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            $original = $content;
            $content = preg_replace_callback('/number_format\s*\(\s*((?:[^)(]+|\([^)(]*\))*)\s*(?:,.*?)?\)/', function ($matches) {
                return "format_number({$matches[1]})";
            }, $content);
            if ($content !== $original) {
                file_put_contents($file->getPathname(), $content);
                $count++;
            }
        }
    }
}
echo "Finished checking all files. Total updated: $count\n";
