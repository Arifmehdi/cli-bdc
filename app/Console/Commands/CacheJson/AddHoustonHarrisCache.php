<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddHoustonHarrisCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-houston-harris-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Houston - Harris Zip Code data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // here have 4 digit zip code ...

        // $cacheFilePath = storage_path('app/san_antonio_county.json');

        // // Check if the file exists
        // if (!file_exists($cacheFilePath)) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Cache file not found'
        //     ], 404);
        // }

        // // Read the JSON file
        // $jsonData = file_get_contents($cacheFilePath);
        // dump(json_decode($jsonData));
        // dd('53 bilion');
        // // Convert to JSON response
        // return response()->json(json_decode($jsonData), 200);

        $zip_code = [
            "31804", "31807", "31811", "31822", "31823", "31826", "31831", "77001", "77002", "77003",
            "77004", "77005", "77006", "77007", "77008", "77009", "77010", "77011", "77012", "77013",
            "77014", "77015", "77016", "77017", "77018", "77019", "77020", "77021", "77022", "77023",
            "77024", "77025", "77026", "77027", "77028", "77029", "77030", "77031", "77032", "77033",
            "77034", "77035", "77036", "77037", "77038", "77039", "77040", "77041", "77042", "77043",
            "77044", "77045", "77046", "77047", "77048", "77049", "77050", "77051", "77052", "77053",
            "77054", "77055", "77056", "77057", "77058", "77059", "77060", "77061", "77062", "77063",
            "77064", "77065", "77066", "77067", "77068", "77069", "77070", "77071", "77072", "77073",
            "77074", "77075", "77076", "77077", "77078", "77079", "77080", "77081", "77082", "77083",
            "77084", "77085", "77086", "77087", "77088", "77089", "77090", "77091", "77093", "77094",
            "77095", "77096", "77098", "77201", "77202", "77203", "77204", "77205", "77206", "77207",
            "77208", "77209", "77210", "77212", "77213", "77215", "77216", "77217", "77218", "77219",
            "77220", "77221", "77222", "77223", "77224", "77225", "77226", "77227", "77228", "77229",
            "77230", "77231", "77233", "77234", "77235", "77236", "77237", "77238", "77240", "77241",
            "77242", "77243", "77244", "77245", "77248", "77249", "77251", "77252", "77253", "77254",
            "77256", "77257", "77258", "77259", "77261", "77262", "77263", "77265", "77266", "77267",
            "77268", "77269", "77270", "77271", "77272", "77273", "77274", "77275", "77277", "77279",
            "77336", "77338", "77339", "77346", "77373", "77375", "77373", "77388", "77389", "77396",
            "77401", "77429", "77433", "77447", "77449", "77450", "77491", "77493", "77494", "77502",
            "77503", "77504", "77505", "77506", "77507", "77520", "77521", "77530", "77532", "77536",
            "77547", "77562", "77571", "77587", "77598", "79901", "79925", "79902", "79903", "79904",
            "79905", "79907", "79912", "79915", "79922", "79924", "79930", "79932", "79935", "79936",
            "79906", "79908", "79910", "79911", "79913", "79914", "79917", "79920", "79923", "79926",
            "79927", "79929", "79931", "79934", "79937", "79938", "79940", "79941", "79942", "79943",
            "79944", "79945", "79946", "79947", "79948", "79949", "79950", "79951", "79952", "79953",
            "79954", "79955", "79958", "79960", "79961", "79968", "79976", "79978", "79980", "79990",
            "79995", "79996", "79997", "79998", "79999", "88510", "88511", "88512", "88513", "88514",
            "88515", "88517", "88518", "88519", "88520", "88521", "88523", "88524", "88525", "88526",
            "88527", "88528", "88529", "88530", "88531", "88532", "88533", "88534", "88535", "88536",
            "88538", "88539", "88540", "88541", "88542", "88543", "88544", "88545", "88546", "88547",
            "88548", "88549", "88550", "88553", "88554", "88555", "88556", "88557", "88558", "88559",
            "88560", "88561", "88562", "88563", "88565", "88566", "88567", "88568", "88569", "88570",
            "88571", "88572", "88573", "88574", "88575", "88576", "88577", "88578", "88579", "88580",
            "88581", "88582", "88583", "88584", "88585", "88586", "88587", "88588", "88589", "88590",
            "88595"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/harris_county.json'); // File path for caching


        // Clear old cache file if it exists
        if (file_exists($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        // Open the file in append mode
        $file = fopen($cacheFilePath, 'a');

        // Write an opening bracket for JSON array
        fwrite($file, '[');

        $firstChunk = true; // Flag to handle commas between records
        // $mainInventory = MainInventory::whereIn('zip_code',$zip_code)->get();

        $mainInventory = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price', 'exterior_color', 'interior_color', 'fuel', 'body_formated','drive_info', 'transmission','stock_date_formated')
            ->with([
                'dealer' => function ($query) {
                    $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id')
                        ->addSelect('id'); // Add id explicitly to avoid conflict
                },
                'additionalInventory' => function ($query) {
                    $query->select('main_inventory_id', 'local_img_url')  // Only necessary columns
                        ->addSelect('id'); // Add id explicitly to avoid conflict
                },
                'mainPriceHistory' => function ($query) {
                    $query->select('main_inventory_id', 'change_amount') // Only necessary columns
                        ->addSelect('id'); // Add id explicitly to avoid conflict
                }
            ])->whereIn('zip_code', $zip_code)
            // ->get();;
            ->chunk($chunkSize, function ($chunk, $index) use ($file, &$firstChunk) {
                foreach ($chunk as $dealer) {
                    // Serialize each dealer as JSON
                    $dealerJson = json_encode($dealer);

                    // Add a comma between records (except for the first one)
                    if (!$firstChunk) {
                        fwrite($file, ',');
                    } else {
                        $firstChunk = false;
                    }

                    // Write the dealer JSON to the file
                    fwrite($file, $dealerJson);
                }
            });

        fwrite($file, ']');

        // Close the file
        fclose($file);

        $this->info('Houston - Harris data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
