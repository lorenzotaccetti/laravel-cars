<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Car;
use App\Category;
use App\Optional;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cars = Car::all();

        return view('admin.cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = Category::all();
        $optionals = Optional::all();

        $data = [

            'categories'=> $categories,
            'optionals' => $optionals

        ];

        return view('admin.cars.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form_data = $request->all();

        $request->validate($this->getValidationRules());

        $new_car = new Car();

        $new_car->fill($form_data);

        $new_car->slug = $this->getUniqueSlugFromBrand($new_car->brand);
        

        if (isset($form_data['src'])) {
            $img_path = Storage::put('src', $form_data['src']);
            $new_car->src = url('storage/'. $img_path);
        }

        $new_car->save();

        if(isset($form_data['optionals'])) {
            $new_car->optionals()->sync($form_data['optionals']);
        }

        return redirect()->route('admin.cars.show', ['car' => $new_car->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Car $car)
    {
        return view('admin.cars.show', compact('car'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $car = Car::findOrFail($id);

        $categories = Category::all();
        $optionals = Optional::all();

        $data = [

            'car' => $car,
            'categories'=> $categories,
            'optionals' => $optionals

        ];

        return view('admin.cars.edit', $data);
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
        $form_data = $request->all();

        $request->validate($this->getValidationRules());

        $update_car = Car::findOrFail($id);

        if($form_data['brand'] != $update_car->brand ) {
            $form_data['slug'] = $this->getUniqueSlugFromBrand($form_data['brand']);
        }

        if ($form_data['src']) {
            if ($update_car->src) {
                Storage::delete($update_car->src);
            }
            $img_path = Storage::put('src', $form_data['src']);
            $form_data['src'] = url('storage/'.$img_path);
        }

        $update_car->update($form_data);

        if(isset($form_data['optionals'])) {
            $update_car->optionals()->sync($form_data['optionals']);
        } else {
            $update_car->optionals()->sync([]);
        }

        return redirect()->route('admin.cars.show', ['car' => $update_car->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $car_to_delete = Car::findOrFail($id);
        $car_to_delete->optionals()->sync([]);
        $car_to_delete->delete();

        return redirect()->route('admin.cars.index');
    }

    //Funzione di validazione

    protected function getValidationRules()
    {
        return [
        'brand' => 'required|max:30',
        'description' => 'min:10|max:60000|nullable',
        'src' => 'required|max:1000',
        'price' => 'required|max:14',
        'model' => 'required|max:100',
        'cc' => 'required|max:10',
        'category_id' => 'exists:categories,id|nullable',
        'optionals' => 'exists:optionals,id'
        ];
    }

    protected function getUniqueSlugFromBrand($brand)
    {

        $slug = Str::slug($brand);
        $slug_base = $slug;

        $car_found = Car::where('slug', '=', $slug)->first();
        $counter = 1;
        while($car_found) {
            // Se esiste, aggiungiamo -1 allo slug
            // ricontrollo che non esista lo slug con -1, se esiste provo con -2
            $slug = $slug_base . '-' . $counter;
            $car_found = Car::where('slug', '=', $slug)->first();
            $counter++;
        }

        return $slug;

    }
}
