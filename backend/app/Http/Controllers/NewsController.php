<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\News;
use Request;
use Auth;
use Carbon\Carbon;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
class NewsController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

	    if( Request::has('page_info') )
	    {
		    $response = [];
		    $fb = News::whereNotNull('facebook_page_info')->where('company_id',Auth::user()->company_id)->where('share_on_facebook',1)->orderBy('created','desc')->first();
		    $twitter = News::whereNotNull('twitter_page_info')->where('company_id',Auth::user()->company_id)->where('share_on_twitter',1)->orderBy('created','desc')->first();
		    if( $fb )
			    $response['facebook_page_info'] = $fb->facebook_page_info;

		    if( $twitter )
			    $response['twitter_page_info'] = $twitter->twitter_page_info;

		    return response()->json($response);
	    }
        $response = array('published'=>[],'not_published'=>[]);
        /* 2015/06/10  DELETED BY LIU START  */        
	 /*   $response['published'] = News::where(function($q){
            //$q->where('is_published',1)->orWhere('publish_time','<=',date('Y-m-d H:i:s'));//uncomment it when all the cron for posting started working correctly
            $q->where('publish_time','<=',date('Y-m-d H:i:s'));
        })->where('company_id',Auth::user()->company_id)->get();
	    $response['not_published'] = News::where(function($q){
            //$q->where('is_published',0)->orWhere('publish_time','>',date('Y-m-d H:i:s'));//uncomment it when all the cron for posting started working correctly
            $q->where('publish_time','>',date('Y-m-d H:i:s'));
        })->where('company_id',Auth::user()->company_id)->get();*/
        /* 2015/06/10  DELETED BY LIU END  */

         /* 2015/06/10  UPDATED BY LIU START  */
         $response['published'] = News::where('company_id',Auth::user()->company_id)->get();
          /* 2015/06/10  UPDATED BY LIU END  */
        return response()->json($response);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        
        //
        
    }

	private function getFbPage($pageId) {
		$appId = config('facebook.app_id');
		$appSecret = config('facebook.app_secret');
		FacebookSession::setDefaultApplication($appId, $appSecret);
		$session = new FacebookSession(Request::input('access_token'));
		$longLivedToken = $session->getAccessToken()->extend();
		$session = new FacebookSession((string) $longLivedToken);
		$request = new FacebookRequest($session, 'GET', '/me/accounts');
		$pageList = $request->execute()->getGraphObject()->asArray();
		foreach($pageList['data'] AS $page )
		{
			if( $page->id == $pageId)
				return json_encode($page);
		}
		return false;
	}
    
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
	    $data = Request::except(['selected_fb_page','access_token','img']);

	    if( Request::has('access_token') )
	    {
		    $fbPage = $this->getFbPage(Request::input('selected_fb_page'));
		    $data['facebook_page_info'] = $fbPage;
	    }
	    $data['twitter_page_info'] = json_encode($data['twitter_page_info']);

	    $c = Carbon::createFromFormat('Y-m-d H:i:s',$data['publish_time']);
	    $c->timezone = Company::find(Auth::user()->company_id)->Timezone()->first()->php_code;
	    $data['publish_time_eastern'] = $c->toDateTimeString();

	    if( Request::has('img') && Request::input('type') == 'image' )
		{
			if( !preg_match('/http/',Request::input('img') ) )
				$data['image_name'] = $this->saveStringAsImage(str_random().".png",Request::input('img'));
			else
				$data['image_name'] = Request::input('img');
		}
        $data['company_id'] = Auth::user()->company_id;
        if( Request::has('id') )
	    {
		    News::where('id',Request::input('id'))->update($data);
		    return $this->show(Request::input('id'));
	    }
	    else {
		    $data[ 'is_published' ] = 0;
		    $news = News::create( $data );
	    }
        return response()->json($news);
    }

	private function saveStringAsImage($name,$string)
	{
		list($type, $data) = explode(';', $string);
		list(, $data)      = explode(',', $string);
		$data = base64_decode($data);

		$name = 'company_images/'.$name;

		file_put_contents(public_path($name), $data);

		return url($name);
	}
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $news = News::find($id);
        if (!$news) return $this->error('News not found!');
	    return response()->json($news);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        
        //
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        
        //
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {

        News::where('id',$id)->delete();

	    return response()->json(['success'=>'news deleted']);

    }

    public function post($id) {
    	$appId = config('facebook.app_id');
    	$appSecret = config('facebook.app_secret');
        FacebookSession::setDefaultApplication($appId, $appSecret);
		$news = News::find($id);
//        foreach( $newses AS $news )
//        {
            $pageData = json_decode($news->facebook_page_info);
            $session = new FacebookSession($pageData->access_token);
            $fbParams = array('message' => $news->short_description);
            $fbParams['scheduled_publish_time'] = Carbon::createFromFormat('Y-m-d H:i:s',$news->publish_time)->setTimezone('America/New_York')->getTimestamp();//strtotime($news->publish_time);

	        $fbParams['published'] = false;
	        if( ! empty( $news->youtube_url ) )
                $fbParams['link'] = $news->youtube_url;
            else if( !empty( $news->sound_cloud_url ) )
                $fbParams['link'] = $news->sound_cloud_url;
            else
                $fbParams['link'] = $news->image_name;
	        if ($session) {
                try {
                    $response = (new FacebookRequest($session, 'POST', '/'.$pageData->id.'/feed', $fbParams))->execute()->getGraphObject();
                    //echo "Posted with id: " . $response->getProperty('id');
                    $news->is_published = 1;
                    $news->save();
	                return response()->json($news);
                }
                catch(FacebookRequestException $e) {
                    return $this->error($e->getMessage());
                    //echo "Exception occured, code: " . $e->getCode();
                    //echo " with message: " . $e->getMessage();
                }
            }
            else {
                //echo "No Session available!";
            }
        //}
    }
}
