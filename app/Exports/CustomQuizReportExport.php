<?php

namespace App\Exports;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;


use Maatwebsite\Excel\Concerns\FromCollection;

class CustomQuizReportExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     DB::table('user_exam_results')->where(['result_status'=>'completed'])->get();
    // }

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        //dd($this->data);
        $data = $this->data;
        return view('exports.customQuizResult', [
            'data' => $data
        ]);
    }

}
