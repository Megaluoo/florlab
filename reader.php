<?php
$content = file_get_contents("c:\\xampp\\htdocs\\Florlab\\correo\\PrinterServiceBase.dll");
$content .= file_get_contents("c:\\xampp\\htdocs\\Florlab\\correo\\CLTech.PrinterService.exe");
$content = str_replace("\0", "", $content);

preg_match_all('/SELECT[\s\S]{10,200}?FROM[\s\S]{1,50}?(WHERE|ORDER|$)/i', $content, $matches);
print_r(array_unique($matches[0]));

preg_match_all('/Lab[0-9]{2,3}/i', $content, $matches_tables);
print_r(array_unique($matches_tables[0]));
