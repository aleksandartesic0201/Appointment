<?php namespace App\Http\Controllers;

use App\Models\FooterLink;
use App\Models\HeaderLink;
use Auth;
use App\Http\Requests;
use Request;
use App\Models\Company;

class CompaniesController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index ()
	{
		return response()->json( Company::with( [ 'HeaderLinks' , 'FooterLinks','Currency' ] )->find( Auth::user()->company_id ) );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function update ( $id )
	{
		$oCompany = Company::find( $id );

		if ( ! $oCompany )
			return $this->error( "Unable to find company" , 404 );

		$aData = Request::except( [ 'id' , 'header_links' , 'footer_links','currency' ] );
		if ( isset( $aData[ 'password' ] ) AND ! empty( $aData[ 'password' ] ) ) {
			$aData[ 'password' ] = bcrypt( $aData[ 'password' ] );
		}

		Company::where( 'id' , $id )->update( $aData );
		if ( Request::has( 'header_links' ) ) {
			$headerLinks = array ();
			$shouldBeRemovedIds = array();
			foreach ( Request::input( 'header_links' ) AS $link ) {
				if ( isset( $link[ 'id' ] ) )
				{
					$headerLinks[ ] = HeaderLink::find( $link[ 'id' ] );
					$shouldBeRemovedIds[] = $link['id'];
				}
				else
				{
					$headerLink = HeaderLink::create($link);
					$shouldBeRemovedIds[] = $headerLink->id;
					$headerLinks[ ] = $headerLink;
				}
			}
			$oCompany->HeaderLinks()->whereNotIn('id',$shouldBeRemovedIds)->where('company_id',$id)->delete();
			$oCompany->HeaderLinks()->saveMany( $headerLinks );
			//return response()->json($headerLinks);
		}

		if ( Request::has( 'footer_links' ) ) {
			$footerLinks = array ();
			$shouldBeRemovedIds = array();
			foreach ( Request::input( 'footer_links' ) AS $link ) {
				if ( isset( $link[ 'id' ] ) )
				{
					$footerLinks[ ] = FooterLink::find( $link[ 'id' ] );
					$shouldBeRemovedIds[] = $link['id'];
				}
				else
				{
					$footerLink = FooterLink::create( $link );
					$shouldBeRemovedIds[] = $footerLink->id;
					$footerLinks[ ] = $footerLink;
				}
			}
			$oCompany->FooterLinks()->whereNotIn('id',$shouldBeRemovedIds)->where('company_id',$id)->delete();
			$oCompany->FooterLinks()->saveMany( $footerLinks );
		}

		return $this->index();
	}

	public function uploadImage()
	{
		$id = Request::input('id');
		$image_type = Request::input('type');
		$string = Request::input('img');

		list($type, $data) = explode(';', $string);
		list(, $data)      = explode(',', $string);
		$data = base64_decode($data);

		$name = 'company_images/';
		if( $image_type == 'icon' )
			$name .= $id.".png";
		else
			$name .= $id."_".$image_type.".png";
		file_put_contents(public_path($name), $data);

		$company = Company::find($id);
		if( $image_type == 'logo' )
			$company->logo = url($name);
		else
			$company->image = url($name);
		$company->save();

		return $this->index();
	}

}
