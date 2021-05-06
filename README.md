# laravel-validation
###### tags `Laravel 8` `Validation` `scene` `php` `Form Requests`

## 前言
Laravel提供了幾種不同的方法來驗證傳入應用程序的數據。默認情況下，Laravel的基類控制器使用了一個名為ValidatesRequests的trait，它提供了一種方便的方法以使用各種強大的驗證規則來驗證傳入的HTTP請求。

面對更複雜的情況，您可以創建一個「Form Requests」來應對更複雜的驗證邏輯。Form Requests是一個包含了驗證邏輯的自定義請求類。


今天的目標是為Laravel 驗證器加上多驗證場景
## 安裝
新建一個專案
```
$ laravel new laravel-validation
```
## 基本的CRUD 
這邊用跑馬燈做一個示範
```
 # 生成Controller, Model, Factory ,Migration 
 
 $ php artisan make:model Marquee -mf      
 $ php artisan make:controller MarqueeController   
```
```php=
# routes/api.php

Route::prefix('/marquee')->group(function () {
    Route::get('/', [MarqueeController::class, 'index']);

    Route::post('/', [MarqueeController::class, 'store']);

    Route::put('/{id}', [MarqueeController::class, 'update']);

    Route::delete('/{id}', [MarqueeController::class, 'destroy']);
});
```
```php=
# database/migrations/2021_05_05_013851_create_marquees_table.php

public function up()
    {
        Schema::create('marquees', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('forever')->default(2)->comment('長期播放(1:是, 2:否)');
            $table->dateTime('start_at')->nullable()->comment('開始播放時間');
            $table->dateTime('end_at')->nullable()->comment('結束播放時間');
            $table->string('title', 100)->comment('跑馬燈標題');
            $table->string('description', 100)->comment('跑馬燈內容');
            $table->set('display', ['0','1','2','3','4'])->comment('顯示(0:全站, 1:總監, 2:股東, 3:代理, 4: 會員)');
            $table->timestamps();
        });
    }
```
```php=
# app/Models/Marquee.php

protected $fillable = ['title', 'description', 'display', 'forever', 'start_at', 'end_at'];
```
```php=
# app/Http/Controllers/MarqueeController.php

public function index()
    {
        $marquee = Marquee::simplePaginate(15);

        return response()->json([
            'status_code' => 200,
            'result' => $marquee
        ]);
    }

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'display' =>['required'],
        'forever' => ['required', 'in:1,2'],
        'title'       => ['required', 'max:20'],
        'description' => ['required', 'max:500'],
        'start_at'    => ['bail', 'required', 'date_format:Y-m-d H:i:s', 'after_or_equal:' . date("Y-m-d H:i:s", strtotime('-2 minute'))],
        'end_at'      => ['bail', 'required_if:forever,==,2', 'date_format:Y-m-d H:i:s', 'after:start_at']
    ]);

    if ($validator->fails())
    {
        return response()->json([
            'status_code' => 422,
            'error' => $validator->errors()
        ]);
    }

    Marquee::create($request->all());

    return response()->json([
        'status_code' => 200,
        'result' => 'true'
    ]);
}

public function update(int $id, Request $request)
{
    $request['id'] = $id;

    $validator = Validator::make($request->all(), [
        'id'          => ['required','exists:marquees,id'],
        'display' =>['required'],
        'forever' => ['required', 'in:1,2'],
        'title'       => ['required', 'max:20'],
        'description' => ['required', 'max:500'],
        'start_at'    => ['bail', 'required', 'date_format:Y-m-d H:i:s', 'after_or_equal:' . date("Y-m-d H:i:s", strtotime('-2 minute'))],
        'end_at'      => ['bail', 'required_if:forever,==,2', 'date_format:Y-m-d H:i:s', 'after:start_at']
    ]);

    if ($validator->fails())
    {
        return response()->json([
            'status_code' => 422,
            'error' => $validator->errors()
        ]);
    }

    $params = $request->except('id');
    Marquee::find($id)->update($params);

    return response()->json([
        'status_code' => 200,
        'result' => 'true'
    ]);
}

public function destroy(int $id, Request $request)
{
    $request['id'] = $id;
    $validator = Validator::make($request->all(), [
        'id'          => ['required','exists:marquees,id'],
    ]);

    if ($validator->fails())
    {
        return response()->json([
            'status_code' => 422,
            'error' => $validator->errors()
        ]);
    }

    Marquee::find($id)->delete();

    return response()->json([
        'status_code' => 200,
        'result' => 'true'
    ]);
}
```
做完記得migrate
```
$ php artisan migrate     
```
## 建立 Form Requests
因為每個驗證的需求不一樣，所以建了三個
```
$ php artisan make:request MarqueeStoreRequest 
$ php artisan make:request MarqueeUpdateRequest 
$ php artisan make:request MarqueeDestoryRequest
```
記得把每個 false 改成 true
```php
# app/Http/Requests/MarqueeStoreRequest.php

public function authorize()
{
    return true;
}
```


接下來改用 Form Requests 來驗證
```php=
# app/Http/Requests/MarqueeStoreRequest.php
public function rules()
{
    return [
        'display'     => ['required'],
        'forever'     => ['required', 'in:1,2'],
        'title'       => ['required', 'max:20'],
        'description' => ['required', 'max:500'],
        'start_at'    => ['bail', 'required', 'date_format:Y-m-d H:i:s', 'after_or_equal:' . date("Y-m-d H:i:s", strtotime('-2 minute'))],
        'end_at'      => ['bail', 'required_if:forever,==,2', 'date_format:Y-m-d H:i:s', 'after:start_at']
    ];
}
```

```php=
# app/Http/Controllers/MarqueeController.php

public function store(MarqueeStoreRequest $request)
    {
        Marquee::create($request->all());

        return response()->json([
            'status_code' => 200,
            'result' => 'true'
        ]);
    }
```
路由上的參數可用下面的方法帶入
```php=
# app/Http/Requests/MarqueeUpdateRequest.php
public function prepareForValidation()
{
    return $this->merge(['id' => $this->route('id')]);
}
```
### 統一api的格式
我們來改寫 app/Exceptions/Handler.php

我們到父層找到下面這個方法
```php=
public function render($request, Throwable $e)
    {
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($e instanceof Responsable) {
            return $e->toResponse($request);
        }

        $e = $this->prepareException($this->mapException($e));

        foreach ($this->renderCallbacks as $renderCallback) {
            if (is_a($e, $this->firstClosureParameterType($renderCallback))) {
                $response = $renderCallback($e, $request);

                if (! is_null($response)) {
                    return $response;
                }
            }
        }

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        return $request->expectsJson()
                    ? $this->prepareJsonResponse($request, $e)
                    : $this->prepareResponse($request, $e);
    }
```
在這邊會檢查錯誤從哪來而有不同的回傳方式
```php=
if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }
```
有需要的話可以在子層改寫render

這邊選擇在子層改寫 convertValidationExceptionToResponse

```php=
# 父層
# src/Illuminate/Foundation/Exceptions/Handler.php

protected function convertValidationExceptionToResponse(ValidationException $e, $request)
{
    if ($e->response) {
        return $e->response;
    }

    return $request->expectsJson()
                ? $this->invalidJson($request, $e)
                : $this->invalid($request, $e);
    }
```
```php=
# 子層
# app/Exceptions/Handler.php

protected function convertValidationExceptionToResponse(ValidationException $e, $request)
{
    if ($e->response) {
        return $e->response;
    }

    return $request->expectsJson()
        ? response()->json([
            'status_code' => 422,
            'error'       => $e->errors()
        ])
        : $this->invalid($request, $e);
}
```

這邊就完成我們第一階段的改造

但光一個 CRUD 就建了3個 Form Requests 

下一步會加上驗證場景 讓我們同一個 Form Requests 就可以完成驗證的工作