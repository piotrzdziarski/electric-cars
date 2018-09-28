<?php

namespace App\Http\Controllers;

class SitesController extends Controller
{
    /**
     * Home page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home() {
        return view('home');
    }


    /**
     * Cars subpage
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cars() {
        return view('sites.cars');
    }
    /**
     * Car subpage
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function car() {
        return view('sites.car');
    }


    /**
     * announcements subpage
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function announcements(){
        return view('sites.announcements');
    }

}
