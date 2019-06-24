<?php

return [
    'connection' => ENV('PRIMECARGO_CONNECTION', 'local-ftp'),
    'fileout' => ENV('PRIMECARGO_FILEOUT', 'Export') . DIRECTORY_SEPARATOR,
    'filein' => ENV('PRIMECARGO_FILEIN', 'Import') . DIRECTORY_SEPARATOR,
];