<?php
/*
 * ITU Project 2019/2020
 * Flight Search (Team xjurig00, xlinka01, xpukan01)
 *
 * Author of this file: Dominik Juriga (xjurig00)
 *
 * */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $r)
    {
        $companies = json_decode(file_get_contents('airlines.json'));
        $path = storage_path() . "\airports.json";
        $json = json_decode(file_get_contents($path), true);
        $from = null;
        $to = null;
        foreach ($json as $item) {
            if (strpos(strtolower($item['name']), strtolower($r->input('search_form_from'))) !== false ||
                strpos(strtolower($item['city']), strtolower($r->input('search_form_from'))) !== false) {
                $from = $item['iata'];
            }
            if (strpos(strtolower($item['name']), strtolower($r->input('search_form_to'))) !== false ||
                strpos(strtolower($item['city']), strtolower($r->input('search_form_to'))) !== false) {
                $to = $item['iata'];
            }
        }
        if ($from == null || $to == null) {
            abort(404);
        }

        $dateFrom
            = $r->input('search_form_dateFrom');
        $dateTo
            = $r->input('search_form_dateTo');

        $adults = $r->input('search_form_adults') ?? 1;
        $children = $r->input('search_form_children') ?? 0;
        $infants = $r->input('search_form_infants') ?? 0;

        $total_passengers = intval($adults) + intval($children) + intval($infants);

        $sort = $r->input('search_form_sort') ?? 'price';
        $time = substr($r->input('search_form_timeFrom'), 0, 5) ?? '00:00';

        $url
            = 'https://api.skypicker.com/flights?flyFrom=' .
            $from . '&to=' . $to . '&dateFrom=' . $dateFrom . '&dateTo=' . $dateTo .
            '&adults=' . $adults . '&children=' . $children . "&infants=" . $infants .
            '&sort=' . $sort . '&dtime_from=' . $time .
            '&partner=picky&limit=10' ;

        $date = date( "d/m/Y", strtotime(now()));

        $data = json_decode(file_get_contents($url))->data ?? array();

        //dd($data);

        if (array_key_exists('search_form_from', $_GET)) {
            $s_value = $_GET['search_form_from'];
        }
        else {
            if (auth()->user()->preferred_airport) {
                $s_value = auth()->user()->preferred_airport;
            }
            else {
                $s_value = "";
            }
        }


        return view('home', ['flights' => $data ?? array(), 'date' => $date, 'companies' => $companies, 'sort' => $sort, 'pas_count' => $total_passengers, 'search_value' => $s_value]);
    }
}
