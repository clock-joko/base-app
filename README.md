# base-app

## Baseの設定 

### 0 . composer install
追加したいLaravelプロジェクトのcomposer.jsonに追記  
```
"require": {
   "clock/base": "dev-master"
},
"repositories": [
       { "packagist": false },
       {
           "type": "vcs",
           "url": "git@gitlab.com:clock-it/base-app.git"
       }
],
```  
$ composer install  

### 1. php artisan vendor:publish
configを反映  

config/base.phpを作成  

default : DataSources  
envに設定すれば配置ディレクトリを変更可能  
設定値 : DATASOURCE_PATH  

### 2. php artisan make:database テーブル名
共通テーブルのmigrationファイルおよびSeederの作成  
(現状、都道府県マスタのみ対応)  

※src/Databases内に用意されているものだけ作成可能  

### 3. php artisan make:model モデル名 -y -i 
モデル作成 

-y ： リポジトリ作成  
-i ： インターフェイス作成  

### 4.RepositoryServiceProviderを追加（手動）
作成した各RepositoryとInterfaceをbindする  

＜参考＞
app/Providers/RepositoryServiceProvider.php
```
<?php
namespace App\Providers;

use App\DataSources\MRoles\Repositories\MRoleRepository;
use App\DataSources\MRoles\Interfaces\MRoleRepositoryInterface;
use App\DataSources\TUsers\Repositories\TUserRepository;
use App\DataSources\TUsers\Interfaces\TUserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            TUserRepositoryInterface::class,
            TUserRepository::class
        );

        $this->app->bind(
            MRoleRepositoryInterface::class,
            MRoleRepository::class
        );
    }
}
```

### その他
リポジトリのみ作成  
$ php artisan make:repository モデル名  

インターフェースのみ作成  
$ php artisan make:interface モデル名


## Baseロジック
通常のCRUDは用意されている  
各Repositoryでは、特殊な操作のみを追加する  
(Interfaceも同様)  

各モデルでは、where条件やrelationの設定のみが基本  
DB操作自体は各Repositoryに記載

### 基本記述
＜参考(適当です)＞  
app/DataSources/Repositories/TUserRepository.php  
```
<?php

namespace App\DataSources\TUsers\Repositories;

use Clock\Baserepo\Repositories\BaseRepository;
use App\DataSources\TUsers\TUser;
use App\DataSources\TUsers\Interfaces\TUserRepositoryInterface;

class TUserRepository extends BaseRepository implements TUserRepositoryInterface
{
    /**
     * TUserRepository constructor.
     *
     * @param TUser $tUser
     */
    public function __construct(TUser $tUser)
    {
        parent::__construct($tUser);
        $this->model = $tUser;
    }

    /**
     * ユーザーリスト取得
     *
     * @param array $columns
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function getUserList($columns = ['*'], int $limit = 15)
    {
        return $this->model->with('roles')->select($columns)->paginate($limit);
    }

    /**
     * アカウント作成
     *
     * @param array $params
     * @return TUser
     * @throws \Exception
     */
    public function createAccount(array $params) : TUser
    {
        try {
            $data = collect($params)->except('password')->all();

            $tUser = new TUser($data);
            $tUser->password = bcrypt($params['password']);

            $tUser->save();

            return $tUser;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 500, $e);
        }
    }
}
```  

app/DataSources/Requests/CreateTUserRequest.php  
```
<?php

namespace App\DataSources\TUsers\Requests;

use Clock\Baserepo\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreateTUserRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:t_users'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['required', 'numeric', Rule::exists('m_roles', 'id')],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() : array
    {
        return [
            'name.required' => '氏名は必須です。',
            'name.max' => '氏名は50文字まで入力可能です。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => 'メールアドレス形式で入力して下さい。',
            'email.max' => 'メールアドレスは191文字以内で入力して下さい。',
            'email.unique' => 'そのメールアドレスはすでに使われています。',
            'password.required' => 'パスワードは必須です。',
            'password.min' => 'パスワードは6文字以上で入力して下さい。',
            'role_id.required' => '権限を設定してください。',
            'role_id.exists' => '権限設定が不正です。',
        ];
    }
}
```  

app/Http/Controllers/UserController.php  
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\DataSources\TUsers\Requests\CreateTUserRequest;
use App\DataSources\TUsers\Interfaces\TUserRepositoryInterface;

class UserController extends Controller
{
    /**
     * @var
     */
    private $tUserRepo;

    /**
     * UserController constructor.
     *
     * @param TUserRepositoryInterface $tUserRepo
     */
    public function __construct(TUserRepositoryInterface $tUserRepo)
    {
        $this->middleware('auth');

        $this->tUserRepo = $tUserRepo;
    }

    /**
     * アカウント管理
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = $this->tUserRepo->getUserList();

        return view('user.index', [
            'users' => $users,
        ]);
    }

    /**
     * アカウント登録
     *
     * @param CreateTUserRequest $request
     * @return RedirectResponse
     */
    public function store(CreateTUserRequest $request) : RedirectResponse
    {
        $this->tUserRepo->createAccount($request->except('_method', '_token'));
        session()->flash('status', __('アカウント登録が完了しました'));

        return redirect()->route('users.index');
    }
}
```
