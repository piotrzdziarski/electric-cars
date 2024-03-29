<?php

namespace App\Http\Controllers;

use App\Advert;
use App\ComparisionList;
use App\ComparisionProduct;
use App\Http\Resources\AdvertResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AdvertsController extends Controller
{
    /**
     * @var Advert
     */
    private $advert;
    private $looking_for;

    /**
     * Constructor with Advert DI
     * @param Advert $advert
     */
    public function __construct(Advert $advert)
    {
        $this->advert = $advert;
    }

    /**
     * Sorting records by user choose - sortBy.vue
     * @param $adverts
     * @param $per_page
     * @param $order_by
     * @return mixed
     */
    private function sorting($adverts, $per_page, $order_by)
    {

        switch ($order_by) {
            case 'newest':
                $adverts = $adverts->orderBy('id', 'desc');
                break;
            case 'oldest':
                $adverts = $adverts->orderBy('id', 'asc');
                break;
            case 'name':
                $adverts = $adverts->orderBy('title', 'asc');
                break;
            case 'lowest_price':
                $adverts = $adverts->orderBy('price', 'asc');
                break;
            case 'highest_price':
                $adverts = $adverts->orderBy('price', 'desc');
                break;

            default:
                $adverts = $adverts->orderBy('id', 'desc');
                break;
        }

        $adverts = $adverts->select(
            'id',
            'title',
            'year',
            'mileage',
            'country',
            'location',
            'type_of_drive',
            'engine',
            'torque',
            'body_style',
            'exterior_color',
            'price',
            'date'
        )->paginate($per_page);

        return $adverts;
    }

    private function basicSearching($adverts, $looking_for)
    {

        $this->looking_for = $looking_for;
        $this->advert = $adverts;

        /* need to use query for basic searching, because if user would use advanced searching
        where clauses would be like where(//)->orWhere(//)->where(//) -
        this would return inaccurate results */
        $adverts = $adverts->where(function ($query) {
            $query->where('title', 'like', '%' . $this->looking_for . '%')
                ->orWhere('make', 'like', '%' . $this->looking_for . '%')
                ->orWhere('model', 'like', '%' . $this->looking_for . '%')
                ->orWhere('exterior_color', 'like', '%' . $this->looking_for . '%')
                ->orWhere('year', 'like', '%' . $this->looking_for . '%')
                ->orWhere('body_style', 'like', '%' . $this->looking_for . '%')
                ->orWhere('condition', 'like', '%' . $this->looking_for . '%')
                ->orWhere('torque', 'like', '%' . $this->looking_for . '%');
        });

        return $adverts;
    }

    private function advanced_search($adverts, $user_settings, $min_price, $max_price)
    {

        if ($min_price != '') {
            $adverts = $adverts->where('price', '>', (int)$min_price);
        }
        if ($max_price != '') {
            $adverts = $adverts->where('price', '<', (int)$max_price);
        }


        $index = 0;
        if ($user_settings == []) {
            return $adverts;
        }
        //foreach option in advanced searching, frontend is rendered from that const too
        foreach (Config::get('constants.SEARCHING_SETTING') as $settingName => $setting) {

            //column in database have underscore instead of space
            $settingName = str_replace(' ', '_', $settingName);

            //values are retrieved from setting.vue - data structure is indexed arrays in outer indexed array
            $choosenSettingValues = $user_settings[$index];

            //first option in advanced searching is Any - filter results only if Any is not checked and if array isnt empty
            if ($choosenSettingValues != []) {
                if ($choosenSettingValues[0] !== "Any") {
                    $adverts = $adverts->whereIn("$settingName", $choosenSettingValues);
                }
            }

            $index++;
        }

        return $adverts;
    }

    /**
     * Adverts retrieve
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $per_page = $request->get('per_page');
        $order_by = $request->get('order_by');
        $looking_for = $request->get('looking_for');
        $user_settings = $request->get('user_settings');
        $min_price = $request->get('min_price');
        $max_price = $request->get('max_price');
        $adverts = $this->advert;

        if($user_settings != []) {
            $adverts = $this->advanced_search($adverts, $user_settings, $min_price, $max_price);
        }
        if($looking_for != '') {
            $adverts = $this->basicSearching($adverts, $looking_for);
        }
        $adverts = $this->sorting($adverts,$per_page, $order_by);

        return AdvertResource::collection($adverts);
    }


    /**
     * Single advert page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function advert($id)
    {
        //if previously cached retrieve
        if(Cache::has(URL::current())) {
            return Cache::get(URL::current());
        }

        $advert = $this->advert->find($id);
        $features = $advert->features;

        return Cache::rememberForever(URL::current(), function() use($advert, $features) {
            return View::make('sites.announcement')->with([
                'advert' => $advert,
                'features' => $features,
            ])->render();
        });
    }
}
