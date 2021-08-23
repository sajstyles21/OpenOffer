<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Str;
use Validator;

class ApiController extends Controller
{

    private const STARTDATE = '2020-1-1';
    private const ENDDATE = '2021-8-1';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('api');
    }

    /**
     * @OA\Post(
     *      path="/api/count_properties_by_zipcode",
     *      operationId="api.propertiesCount",
     *      tags={"API"},
     *      summary="Api properties count by zipcode",
     *      description="Api properties count by zipcode",
     *      @OA\Parameter(ref="#/components/parameters/X-localization"),
     *      @OA\Parameter(
     *          name="zipcode",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ApiModel")
     *       ),
     *     )
     */

    public function getCountPropertiesByZipcode(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'zipcode' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response(array('success' => 0, 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }

            //Get column names
            $getColumnNames = DB::connection('mysql')->table('mapping')
                ->join('mapping_attribute', 'mapping.mapping_attribute_id', '=', 'mapping_attribute.id')
                ->where('mapping_attribute.attr_name', '=', 'zip')
                ->select('stage_name', 'resource')->get()->keyBy('resource');

            //Get Data required
            $query = DB::connection('mysql2')->table('re1')
                ->where($getColumnNames['RE1']->stage_name, $request->zipcode)
                ->where('L_ListingDate', '>=', self::STARTDATE)
                ->where('L_ListingDate', '<=', self::ENDDATE)
                ->select('id')->unionAll(DB::connection('mysql2')->table('ri2')
                    ->where($getColumnNames['RI2']->stage_name, $request->zipcode)
                    ->where('L_ListingDate', '>=', self::STARTDATE)
                    ->where('L_ListingDate', '<=', self::ENDDATE)
                    ->select('id'));

            $data = DB::connection('mysql2')->table(DB::raw("({$query->toSql()}) AS final"))
                ->mergeBindings($query)
                ->select('id')
                ->count();

            return response([
                'success' => 1, 'statuscode' => 200,
                'message' => __('Data fetched successfully!'), 'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response(['success' => 0, 'statuscode' => 400, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/avg_price_by_zipcode",
     *      operationId="api.avgPrice",
     *      tags={"API"},
     *      summary="Api avg price by zipcode",
     *      description="Api avg price by zipcode",
     *      @OA\Parameter(ref="#/components/parameters/X-localization"),
     *      @OA\Parameter(
     *          name="zipcode",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ApiModel")
     *       ),
     *     )
     */
    public function getAvgPriceByZipcode(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'zipcode' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response(array('success' => 0, 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }

            //Get column names
            $getColumnNamesZip = DB::connection('mysql')->table('mapping')
                ->join('mapping_attribute', 'mapping.mapping_attribute_id', '=', 'mapping_attribute.id')
                ->where('mapping_attribute.attr_name', '=', 'zip')
                ->select('stage_name', 'resource')->get()->keyBy('resource');

            $getColumnNamesPrice = DB::connection('mysql')->table('mapping')
                ->join('mapping_attribute', 'mapping.mapping_attribute_id', '=', 'mapping_attribute.id')
                ->where('mapping_attribute.attr_name', '=', 'price')
                ->select('stage_name', 'resource')->get()->keyBy('resource');

            //Get Data required
            $query = DB::connection('mysql2')->table('re1')
                ->where($getColumnNamesZip['RE1']->stage_name, $request->zipcode)
                ->where('L_ListingDate', '>=', self::STARTDATE)
                ->where('L_ListingDate', '<=', self::ENDDATE)
                ->select('id', DB::raw('' . $getColumnNamesPrice['RE1']->stage_name . ' as value'))->unionAll(DB::connection('mysql2')->table('ri2')
                    ->where($getColumnNamesZip['RI2']->stage_name, $request->zipcode)
                    ->where('L_ListingDate', '>=', self::STARTDATE)
                    ->where('L_ListingDate', '<=', self::ENDDATE)
                    ->select('id', DB::raw('' . $getColumnNamesPrice['RI2']->stage_name . ' as value')));

            $data = DB::connection('mysql2')->table(DB::raw("({$query->toSql()}) AS final"))
                ->mergeBindings($query)
                ->select('id', 'value')
                ->avg('value');

            return response([
                'success' => 1, 'statuscode' => 200,
                'message' => __('Data fetched successfully!'), 'data' => round($data, 2),
            ], 200);

        } catch (\Exception $e) {
            return response(['success' => 0, 'statuscode' => 400, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/avg_days_by_zipcode",
     *      operationId="api.avgDays",
     *      tags={"API"},
     *      summary="Api avg days by zipcode",
     *      description="Api avg days by zipcode",
     *      @OA\Parameter(ref="#/components/parameters/X-localization"),
     *      @OA\Parameter(
     *          name="zipcode",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ApiModel")
     *       ),
     *     )
     */
    public function getAvgDaysByZipcode(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'zipcode' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response(array('success' => 0, 'statuscode' => 400, 'message' =>
                    $validator->getMessageBag()->first()), 400);
            }

            //Get column names
            $getColumnNamesZip = DB::connection('mysql')->table('mapping')
                ->join('mapping_attribute', 'mapping.mapping_attribute_id', '=', 'mapping_attribute.id')
                ->where('mapping_attribute.attr_name', '=', 'zip')
                ->select('stage_name', 'resource')->get()->keyBy('resource');

            $getColumnNamesStatus = DB::connection('mysql')->table('mapping')
                ->join('mapping_attribute', 'mapping.mapping_attribute_id', '=', 'mapping_attribute.id')
                ->where('mapping_attribute.attr_name', '=', 'status')
                ->select('stage_name', 'resource')->get()->keyBy('resource');

            //Get Data required
            $query = DB::connection('mysql2')->table('re1')
                ->where($getColumnNamesZip['RE1']->stage_name, $request->zipcode)
                ->where($getColumnNamesStatus['RE1']->stage_name, 'SOLD')
                ->where('L_ListingDate', '>=', self::STARTDATE)
                ->where('L_ListingDate', '<=', self::ENDDATE)
                ->select('id', DB::raw("DATEDIFF(L_StatusDate,L_ListingDate)AS days"))->unionAll(DB::connection('mysql2')->table('ri2')
                    ->where($getColumnNamesZip['RI2']->stage_name, $request->zipcode)
                    ->where($getColumnNamesStatus['RI2']->stage_name, 'SOLD')
                    ->where('L_ListingDate', '>=', self::STARTDATE)
                    ->where('L_ListingDate', '<=', self::ENDDATE)
                    ->select('id', DB::raw("DATEDIFF(L_StatusDate,L_ListingDate)AS days")));

            $data = DB::connection('mysql2')->table(DB::raw("({$query->toSql()}) AS final"))
                ->mergeBindings($query)
                ->select('id', 'days')
                ->avg('days');

            return response([
                'success' => 1, 'statuscode' => 200,
                'message' => __('Data fetched successfully!'), 'data' => round($data, 2),
            ], 200);

        } catch (\Exception $e) {
            return response(['success' => 0, 'statuscode' => 400, 'message' => $e->getMessage()], 400);
        }
    }
}
