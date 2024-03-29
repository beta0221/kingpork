<?php
namespace App\Helpers;

use Illuminate\Http\Request;

class Pagination{

    public $page = 1;
    public $rows = 10;
    public $skip = 0;
    public $ascOrdesc = 'desc';
    public $orderBy = 'id';

    public function __construct(Request $request)
    {
        if($request->page){
            $this->page = $request->page;
        }
        if($request->rowsPerPage){
            $this->rows = $request->rowsPerPage;
        }
        if($request->ascending){
            $this->ascOrdesc = 'asc';
        }
        if($request->sortBy){
            $this->orderBy = $request->sortBy;
        }

        $this->skip = ($this->page - 1) * $this->rows;
    }
}