<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreMemberRequest;
use App\Mail\EmailRegisteredVerification;


class MembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            $members = Role::where('name', 'member')->first()->users;
            
            return Datatables::of($members)
               ->addColumn('action', function ($member) {
                    return view('datatable._action', [
                        'detail_url' => route('members.show', $member->id),
                        'edit_url' => route('members.edit', $member->id),
                        'delete_url' => route('members.destroy', $member->id),
                        'confirm_message' => 'Yakin akan menghapus '.$member->name
                    ]);
                })->toJson();
                
        }
        $html = $builder->columns([
            ['data' => 'name', 'name' => 'name', 'title' => 'Members Name' ],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email' ],
            ['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false ],
        ]);

        return view('members.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMemberRequest $request)
    {
        $password = str_random(6);
        $data = $request->all();
        $data['password'] = bcrypt($password);

        // dd($data);
        // tanpa verifikasi 
        $data['is_verified'] = 1;

        $member = User::create($data);

        $memberRole = Role::where('name', 'member')->first();

        $member->attachRole($memberRole);
        
        Mail::to($member)->send(new EmailRegisteredVerification($member, $password));

        return redirect()->route('members.index')->with('flash_notification', [
            'level' => 'success',
            'message' => "Anda Berhasil mendaftarkan Member dengan email $member->email dan password <strong> $password </strong>",

        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
