<?php

namespace App\Console\Commands;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Challan;
use App\Models\ChallanDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use Illuminate\Console\Command;

class Patch extends Command
{

    const PATCH_VERSION_0_1 = '0.1'; // update challan_detail_id
    const PATCH_VERSION_0_2 = '0.2';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apply:patch {ver} {--rollback}';

    /**
     * The console command description.
     *
     * @var string   php artisan apply:patch 0.1
     */
    protected $description = 'Apply Patch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        switch ($this->argument('ver')) {
            case self::PATCH_VERSION_0_1:
                $this->patch01();
            case self::PATCH_VERSION_0_2:
                $this->patch02();
                break;
        }
    }

    public function patch01(){
        if ($this->option('rollback')) {
            return;
        }

        $invoices = Invoice::whereNotNull('challan_ids')->get();

        foreach ($invoices as $invoice) {
            // Step 2: Loop through each challan_id in the invoice
            $challan_ids = explode(',', $invoice->challan_ids); // Get array of challan_ids

            foreach ($challan_ids as $challan_id) {
                // Step 3: Fetch challan details for the given challan_id
                $challan_details = ChallanDetail::where('challan_id', $challan_id)->whereNull('is_receive')->get();

                foreach ($challan_details as $challan_detail) {
                    $product_id = $challan_detail->product_id;
                    $serial_number_id = $challan_detail->serial_number_id;

                    // $this->info("Challen detail id " . $challan_detail->id . " is updating");
                    // if($this->confirm('are you sure you want to update',true)){
                    InvoiceDetail::where('invoice_id', $invoice->id)
                    ->where('product_id', $product_id)
                    ->where('serial_number_id', $serial_number_id)
                    ->update(['challan_detail_id' => $challan_detail->id]);
                    // }
                }
            }
        }
        $this->info("Update Successfully");
        // return response()->json(['message' => 'Challan details updated successfully']);
    }

    public function patch02(){
        //for customer and company same
        $now = Carbon::now();
        $customers = DB::table('customers as c')
            ->join('companies as cmp', function ($join) {
                $join->on('c.customer_name', '=', 'cmp.name')
                    ->whereColumn('c.gst_no', 'cmp.gst_no'); // GST must match
            })
            ->whereNull('c.party_id')
            ->whereNull('cmp.party_id')
            ->select([
                'c.customer_name as name',
                DB::raw('COALESCE(c.contact_no, cmp.contact_no) as contact_no'),
                DB::raw('COALESCE(c.gst_no, cmp.gst_no) as gst_no'),
                DB::raw("'yes' as is_customer"),
                DB::raw("'yes' as is_company"),
                DB::raw("'$now' as created_at"),
                DB::raw("'$now' as updated_at"),
            ])
            ->get();

        $records = $customers->map(function ($item) {
            return [
                'name'        => $item->name,
                'contact_no'  => $item->contact_no,
                'gst_no'      => $item->gst_no,
                'is_customer' => $item->is_customer,
                'is_company'  => $item->is_company,
                'created_at'  => $item->created_at,
                'updated_at'  => $item->updated_at,
            ];
        })->toArray();

        DB::table('party')->insert($records);

        // Update `party_id` in customers where it is null and name matches
        DB::table('customers as c')
        ->join('party as p', 'c.customer_name', '=', 'p.name')
        ->whereNull('c.party_id')
        ->update([
            'c.party_id' => DB::raw('p.id'),
        ]);

        // // Update `party_id` in companies where it is null and name matches
        DB::table('companies as cmp')
        ->join('party as p', 'cmp.name', '=', 'p.name')
        ->whereNull('cmp.party_id')
        ->update([
            'cmp.party_id' => DB::raw('p.id'),
        ]);
    }

}
