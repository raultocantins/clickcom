<?php

namespace App\Imports;
use Illuminate\Support\Str;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProdutoImport implements ToCollection
{

    public function collection(Collection $rows)
    {
    }

}
