<?php

namespace App\Http\Controllers\Admin;

use App\Column;
use App\Module;
use App\ModuleInstance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Redirect;
use Validator;

class ModuleController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');
        
        // Use app\Policies\ModulePolicy for authorizing ressource controller
        $this->authorizeResource(ModuleInstance::class, 'module');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules = ModuleInstance::orderBy('name')->paginate(10);

        return view('admin.module.list', compact('modules'));
    }

    /**
     * Show the form to select the type of the new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        $this->authorize('create', ModuleInstance::class);

        $modules = Module::orderBy('name')->get();

        // Check for existing modules, otherwise redirect back with warning message
        if ($modules->isEmpty()) {
            return redirect()->route('module.index')
                             ->with('warning', __('modules.no_module_templates'));
        }

        return view('admin.module.new', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $template = Module::find(intval($request->module));
        $columns = Column::orderby('description')->get();

        return view('admin.module.create', compact('template', 'columns'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'module' => 'required|integer',
            'name' => 'required|unique:module_instances,name|string|max:255',
            'description' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'item' => 'nullable|integer',
            'option.*' => 'nullable|string|max:255',
            'column.*' => 'nullable|integer',
        ]);

        // Get module dependent config options
        $config = $request->input('option');
        if ($request->has('column')) {
            $config['columns'] = $request->input('column');
        }

        $data = [
            'module_fk' => $request->input('module'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'position' => $request->input('position'),
            'item_fk' => $request->input('item'),
            'config' => $config,
        ];

        ModuleInstance::create($data);

        return redirect()->route('module.index')
            ->with('success', __('modules.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ModuleInstance  $module
     * @return \Illuminate\Http\Response
     */
    public function show(ModuleInstance $module)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ModuleInstance  $module
     * @return \Illuminate\Http\Response
     */
    public function edit(ModuleInstance $module)
    {
        $template = $module->template;
        $columns = Column::orderby('description')->get();

        return view('admin.module.edit', compact('module', 'template', 'columns'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ModuleInstance  $module
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ModuleInstance $module)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('module_instances')->ignore($module),
                'string',
                'max:255',
            ],
            'description' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'item' => 'nullable|integer',
            'option.*' => 'nullable|string|max:255',
            'column.*' => 'nullable|integer',
        ]);

        // Get module dependent config options
        $config = $request->input('option');
        if ($request->has('column')) {
            $config['columns'] = $request->input('column');
        }

        $module->name = $request->input('name');
        $module->description = $request->input('description');
        $module->position = $request->input('position');
        $module->item_fk = $request->input('item');
        $module->config = $config;
        $module->save();

        return redirect()->route('module.index')
            ->with('success', __('modules.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ModuleInstance  $module
     * @return \Illuminate\Http\Response
     */
    public function destroy(ModuleInstance $module)
    {
        $module->delete();

        return back()->with('success', __('modules.deleted'));
    }
}
