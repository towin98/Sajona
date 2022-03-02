<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait paginationTrait {

    /**
     * Crea data paginada
     *
     * @param Request $request
     * @param array $registros
     * @param integer $length
     * @return object
     */
    public function paginar(Request $request, $registros, $length)
    {
        $pageName = 'page';
        $currentPage = $request->page;
        if ($currentPage == null) {
            $currentPage = 1;
        }
        $currentElements = array_slice($registros, $length * ($currentPage - 1), $length);

        $page = Paginator::resolveCurrentPage($pageName);
        $registros =  new LengthAwarePaginator($currentElements, count($registros), $length, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        return $registros;
    }
}
?>
