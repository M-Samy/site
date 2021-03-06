<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Repositories\ProjectRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ProjectController extends AppBaseController
{
    /** @var  ProjectRepository */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepo)
    {
        $this->projectRepository = $projectRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the Project.
     *
     * @param Request $request
     * @return Response
     */

    public function getProjects(){
        $projects = $this->projectRepository->all();
        return view('website.projects')
            ->with('projects', $projects);
    }

    public function getProject($id){
        $project = $this->projectRepository->findWithoutFail($id);
        return view('website.project')
            ->with('project', $project);
    }
    public function index(Request $request)
    {
        $this->projectRepository->pushCriteria(new RequestCriteria($request));
        $projects = $this->projectRepository->all();

        return view('projects.index')
            ->with('projects', $projects);
    }

    /**
     * Show the form for creating a new Project.
     *
     * @return Response
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created Project in storage.
     *
     * @param CreateProjectRequest $request
     *
     * @return Response
     */
    public function store(CreateProjectRequest $request)
    {
        $input = $request->all();
        $files = $request->file('images');
        $images = '';
        if ($files){
            foreach ($files as $file) {
                $imageName = time() . '_' . $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/images/projects');
                if ($file->move($destinationPath, $imageName)) {
                    $images .= $imageName . "\n";
                }
            }
        }
        $input['images'] = $images;
        $project = $this->projectRepository->create($input);

        Flash::success('Project saved successfully.');

        return redirect(route('projects.index'));
    }

    /**
     * Display the specified Project.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $project = $this->projectRepository->findWithoutFail($id);

        if (empty($project)) {
            Flash::error('Project not found');

            return redirect(route('projects.index'));
        }

        return view('projects.show')->with('project', $project);
    }

    /**
     * Show the form for editing the specified Project.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $project = $this->projectRepository->findWithoutFail($id);

        if (empty($project)) {
            Flash::error('Project not found');

            return redirect(route('projects.index'));
        }

        return view('projects.edit')->with('project', $project);
    }

    /**
     * Update the specified Project in storage.
     *
     * @param  int $id
     * @param UpdateProjectRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProjectRequest $request)
    {
        $project = $this->projectRepository->findWithoutFail($id);

        if (empty($project)) {
            Flash::error('Project not found');

            return redirect(route('projects.index'));
        }
        $input = $request->all();
        $files = $request->file('images');
        $images = '';
        if ($files){
            foreach ($files as $file) {
                $imageName = time() . '_' . $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/images/projects');
                if ($file->move($destinationPath, $imageName)) {
                    $images .= $imageName . "\n";
                }
            }
            $input['images'] = $images;
        }


        $project = $this->projectRepository->update($input, $id);

        Flash::success('Project updated successfully.');

        return redirect(route('projects.index'));
    }

    /**
     * Remove the specified Project from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $project = $this->projectRepository->findWithoutFail($id);

        if (empty($project)) {
            Flash::error('Project not found');

            return redirect(route('projects.index'));
        }

        $this->projectRepository->delete($id);

        Flash::success('Project deleted successfully.');

        return redirect(route('projects.index'));
    }
}
