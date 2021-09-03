<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;
use App\Profile_History;
use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }
    public function create(Request $request)
    {
        // 以下を追記
      // Varidationを行う
      $this->validate($request, Profile::$rules);
      $profiles = new Profile;
      $form = $request->all();
     
      // フォームから送信されてきた_tokenを削除する
      unset($form['_token']);
      // フォームから送信されてきたimageを削除する
      unset($form['image']);
      // データベースに保存する
      $profiles->fill($form);
      $profiles->save();
        return redirect('admin/profile/create');
    }

    public function edit(Request $request)
    {
         $profiles = Profile::find($request->id);
      if (empty($profiles)) {
        abort(404);    // ⬅︎Not Foundページを表示
      }
      return view('admin.profile.edit', ['profile_form' => $profiles]);
  }

    

    public function update()
    {
        
         // Validationをかける
      $this->validate($request, Profile::$rules);
      // Profile Modelからデータを取得する
      $profiles = Profile::find($request->id);
      // 送信されてきたフォームデータを格納する
      $profile_form = $request->all();
     

      unset($profile_form['remove']);
      unset($profile_form['_token']);
      

      // 該当するデータを上書きして保存する
      $profiles->fill($profile_form)->save();
      
      //Profile Modelを保存するタイミングで、同時にProfile_History Modelにも編集履歴を追加するよう実装
       $profile_history = new Profile_History;
        $profile_history->profile_id = $profiles->id;
        $profile_history->edited_at = Carbon::now();
        $profile_history->save();


    
        return redirect('admin/profile/edit');
    }
}
