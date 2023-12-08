<?php namespace App\Http\Controllers;

use App\Models\Package;

class PlansController extends Controller
{
	public function index()
	{
		return response()->json(Package::all());
	}
}