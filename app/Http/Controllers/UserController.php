<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Link;
use App\Models\Node;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use SebastianBergmann\Type\ObjectType;

class UserController extends Controller
{
    public function get_data()
    {

        $user = DB::table('users')->selectRaw('users.id,users.name,users.referal_id,users.created_by')->get();
        $array_data = [];
        foreach ($user as $i) {
            array_push($array_data, [$i->id, $i->name, $i->referal_id, $i->created_by]);
        }

        return json_encode($array_data);
    }
    public function show_graph()
    {
        // $user = DB::table('users')->selectRaw('users.id,users.name,users.referal_id,users.created_by')->get();
        // $array_data = [];
        // foreach($user as $i){
        //     array_push($array_data,[$i->id,$i->name,$i->referal_id,$i->created_by]);
        // }
        $user = DB::table('users')->selectRaw('users.id,users.referal_id')->get();
        $array_data = [];
        foreach ($user as $i) {
            array_push($array_data, [$i->id . '', $i->referal_id . '']);
        }
        return view('graph', ['array_data' => json_encode($array_data)]);
    }
    public function referalid()
    {
        // set counter time execution
        // $time_start = microtime(true);
        $user = DB::table('users')->selectRaw('users.id,users.name,users.referal_id')->orderBy('id', 'asc')->get();
        $nodes = [];
        $links = [];
        $error = [];
        foreach ($user as $i) {
            // created node 
            $node = new Node;
            $node->id = $i->id . "-" . $i->name;
            $node->type = "name";
            array_push($nodes, $node);
            if ($i->referal_id != null) {
                // check id is exists
                // $isExistUser = false;
                // foreach ($user as $element) {
                //     if ($i->referal_id == $element->id) {
                //         $isExistUser = true;
                //         break;
                //     }
                // }
                $isExistUser = DB::table('users')->selectRaw('users.id,users.name')->where('users.id', $i->referal_id)->first();
                if ($isExistUser) {
                    // create link to node
                    $link = new Link;
                    $link->source = $i->referal_id . "-" . $isExistUser->name;
                    $link->target = $i->id . "-" . $i->name;
                    array_push($links, $link);
                } else {
                    array_push($error, $i->referal_id . "-" . $i->name);
                }
            }
        }
        // $time_end = microtime(true);
        // $execution_time = ($time_end - $time_start) / 60;
        // dd($error,$execution_time);
        return view('networkgraph.referal_id', ['nodes' => $nodes, 'links' => $links]);
    }
    public function createdby()
    {
         // set counter time execution
        // $time_start = microtime(true);
        $user = DB::table('users')->selectRaw('users.id,users.name,users.created_by')->orderBy('id', 'asc')->get();
        $nodes = [];
        $links = [];
        $error = [];
        foreach ($user as $i) {
            // created node 
            $node = new Node;
            $node->id = $i->id . "-" . $i->name;
            $node->type = "name";
            array_push($nodes, $node);
            if ($i->created_by != null) {
                // check id is exists
                $isExistUser = false;
                foreach ($user as $element) {
                    if ($i->created_by == $element->id) {
                        $isExistUser = true;
                        break;
                    }
                }
                $isExistUser = DB::table('users')->selectRaw('users.id,users.name')->where('users.id', $i->created_by)->first();
                if ($isExistUser) {
                    // create link to node
                    $link = new Link;
                    $link->source = $i->created_by . "-" . $isExistUser->name;
                    $link->target = $i->id . "-" . $i->name;
                    array_push($links, $link);
                } else {
                    array_push($error, $i->created_by . "-" . $i->name);
                }
            }
        }
        // $time_end = microtime(true);
        // $execution_time = ($time_end - $time_start) / 60;
        // dd($error,$execution_time);
        return view('networkgraph.created_by', ['nodes' => $nodes, 'links' => $links]);
    }
}
