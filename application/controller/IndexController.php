<?php

class IndexController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handles what happens when user moves to URL/index/index - or - as this is the default controller, also
     * when user moves to /index or enter your application at base level
     */
    public function index()
    {
        $this->View->render('index/index');
    }
	
	
	 /**
     * Handles what happens when user moves to URL/index/terms - or - as this is the default controller, also
     * when user moves to /terms or enter your application at base level
     */
	public function terms()
    {
         $this->View->render('index/terms');
    }
}
