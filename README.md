# Paraveley
Automatically generate Parsley rules depending on Laravel 5's FormRequest.

## Requirements

- Laravel 5 and later
- Parsley 2

## Installation

You can install Paraveley by choosing any of the options below;

- Add following line to your `composer.json` file  
`"ozanmuyes/paraveley": "0.1.*"`  
and run `composer update` command on your CLI.

- Run `composer require "ozanmuyes/paraveley:0.1.*"` command on your CLI.

## Usage

Add `Ozanmuyes\Paraveley\Traits\FormRequestExtractor;` just below `namespace` declaration and `use FormRequestExtractor;` trait on your Request class.

To retrieve array of Parsley rules for passing them to the view, simply call `$request->parsleyRules()` function.

See **Example** section for further usage information.

## Example

1. `CreateArticleFormRequest.php` file

	```php
	<?php
	
	namespace App\Http\Requests;
	
	use App\Http\Requests\Request;
	use Ozanmuyes\Paraveley\Traits\FormRequestExtractor;
	
	class CreateArticleFormRequest extends Request
	{
	    use FormRequestExtractor;
	
	    /**
	     * Determine if the user is authorized to make this request.
	     *
	     * @return bool
	     */
	    public function authorize()
	    {
	        return false;
	    }
	
	    /**
	     * Get the validation rules that apply to the request.
	     *
	     * @return array
	     */
	    public function rules()
	    {
	        return [
	            "title" => "required|min:3|max:64",
	            "subtitle" => "min:3|max:128",
	            "content" => "required"
	        ];
	    }
	}
	```


2. `ArticlesController.php` file

	```php
	<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	
	use App\Http\Requests;
	use App\Http\Controllers\Controller;
	use App\Article;
	use App\Http\Requests\CreateArticleFormRequest;
	
	class ArticlesController extends Controller
	{		
	    /**
	     * Show the form for creating a new resource.
	     *
	     * @return Response
	     */
	    public function create()
	    {
	        $parsleyRules = (new CreateArticleFormRequest())->parsleyRules();
	
			/* 
			 * $parsleyRules is an array which carries these values depending our 
			 * CreateArticleFormRequest class' rules() function;
			 *
			 * array:3 [
			 *  "title" => array:3 [
			 *    "data-parsley-required" => "true"
			 *    "data-parsley-minlength" => "3"
			 *    "data-parsley-maxlength" => "64"
			 *  ]
			 *  "subtitle" => array:2 [
			 *    "data-parsley-minlength" => "3"
			 *    "data-parsley-maxlength" => "128"
			 *  ]
			 *  "content" => array:1 [
			 *    "data-parsley-required" => "true"
			 *  ]
			 * ]
			 */
	
	        return view("create")->with("parsleyRules", $parsleyRules);
	    }
	}
	```

3. `create.blade.php` file

	- If you are using `LaravelCollective/html` package (which I highly recommend)

		```php
		{!! Form::text("title", null, ["class" => "form-control"] + $parsleyRules["title"]) !!}
		```

	- In case you are still not using the package
	
		```php
		<input name="title" class="form-control" <?php foreach ($parsleyRules["title"] as $key => $value) { echo $key . "='" . $value . "' "; } ?>>
		```

## Remarks

Following Laravel 5 rules are not implemented yet;

- active_url
- after
- array
- before
- confirmed
- date
- exists
- image
- mimes
- required_with
- required\_with\_all
- required_without
- required\_without\_all
- same
- timezone
- unique

See **Roadmap** section for future plans for these rules.

## Roadmap

- Tests will be written and done.
- Due to some Laravel 5 rules relies on database (i.e. `exists`, `unique`) these can NOT applicable on front-end. But other not implemented rules will be implemented soon. Please feel free to fork and create pull request.

## License

The MIT License (MIT)

Copyright (c) 2015 Ozan Müyesseroğlu

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.