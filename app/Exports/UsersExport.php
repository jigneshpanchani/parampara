<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use App\Models\User;
use Illuminate\Http\Request;

class UsersExport implements FromCollection, WithHeadings
{
    protected $request;
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct(Request $request) {
        $this->request = $request;
    }

    public function headings(): array
    {
        $headers = array(
            1 => "Id",
            2 => "First Name",
            3 => "Last Name",
            4 => "Email",
        );

        return $headers;
    }

    public function collection()
    {

        $rq = $this->request;

        // dd($rq);

        $userList = User::get()->toArray();

        if(count($userList) > 0){
            foreach ($userList as $data) {
                $columns1[] = array(
                    1 => $data['id'],
                    2 => $data['first_name'],
                    3 => $data['last_name'],
                    4 => $data['email'],
                );
            }
        }

        return collect($columns1);
    }
}
