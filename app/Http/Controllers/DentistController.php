<?php

namespace App\Http\Controllers;

use App\Http\Requests\addCategoryRequest;
use App\Http\Requests\addItemRequest;
use App\Http\Requests\addSecretary;
use App\Http\Requests\addSecretaryRequest;
use App\Http\Requests\ItemhistoryRequest;
use App\Http\Requests\updatesecretaryequest;
use App\Services\CategoryService;
use App\Services\itemhistoryService;
use App\Services\ItemService;
use App\Services\SecretaryService;
use App\Services\SubCategoryService;
use App\Services\LabmangerService;
use Illuminate\Http\Request;


class DentistController extends Controller
{
    protected $secretaryService;
    protected $categoryService;
    protected $subCategoryService;
    protected $itemService;
    protected $ItemhistoryService;
    protected $LabmangerService;
    public function __construct(SecretaryService $secretaryService, CategoryService $categoryService,SubCategoryService $subCategoryService,ItemService $ItemService,itemhistoryService $ItemhistoryService,LabmangerService $LabmangerService)
    {
        $this->secretaryService = $secretaryService;
        $this->categoryService = $categoryService;
        $this->subCategoryService = $subCategoryService;
        $this->itemService = $ItemService;
        $this->ItemhistoryService=$ItemhistoryService;
        $this->LabmangerService=$LabmangerService;
    }

    public function getSecretaries()
    {
        return $this->secretaryService->getSecretaries();
    }
    public function updateSecretary($id, updatesecretaryequest $request)
    {
        $data = $request->validated();
        return $this->secretaryService->updateSecretary($id, $data);
    }
    public function deleteSecretary($id)
    {
        return $this->secretaryService->removeSecretary($id);
    }
    public function addSecretary(addSecretaryRequest $request)
    {
        $data = $request->validated();
        return $this->secretaryService->addSecretary($data);
    }
    //________________________________________________________________________ادارة مخزون العيادة
    public function getcategories()
    {
        $categories = $this->categoryService->getCategories();
        return $categories;
    }
    public function showSubcategories($categoryId)
    {
        $subcategories = $this->subCategoryService->getSubcategoriesForCategory($categoryId);
        return $subcategories;
    }
    public function addcategory(addCategoryRequest $request)
    {
        $data = $request->validated();
        return $this->categoryService->addcategory($data);
    }
    public function showitems($subcategoryId)
    {
        $items = $this->itemService->getItemsForCategory($subcategoryId);
        return $items;
    }
    public function deletecategory($id)
    {
        return $this->categoryService->removeCategory($id);
    }
    public function updateCategory($id, addCategoryRequest $request)
    {
        $data = $request->validated();
        return $this->categoryService->updateCategory($id, $data);
    }
     public function deleteSubcategory($id)
    {
        return $this->subCategoryService->removeSubCategory($id);
    }
    public function addsubcategory($id,addCategoryRequest $request)
    {
        $data = $request->validated();
        return $this->subCategoryService->addsubcategory($id,$data);
    }
    public function updateSubCategory($id, addCategoryRequest $request)
    {
        $data = $request->validated();
        return $this->subCategoryService->updateSubCategory($id, $data);
    }
    public function additem($id,addItemRequest $request)
    {
        $data = $request->validated();
        return $this->itemService->additem($id,$data);
    }

    public function deleteitem($id)
    {
        return $this->itemService->removeItem($id);
    }

    public function updateitem($id, addItemRequest $request)
    {
        $data = $request->validated();
        return $this->itemService->updateItem($id, $data);
    }
    public function additemhistory($id,ItemhistoryRequest $request)
    {
        $data = $request->validated();
        return $this->ItemhistoryService->additemhistory($id,$data);
    }
    public function itemhistories($itemid)
    {
        $itemhistories = $this->ItemhistoryService->itemhistories($itemid);
        return $itemhistories;
    }

    public function show_labs_dentist_injoied()
    {
        $labs_dentist_joined = $this->LabmangerService->get_labs_dentist_joined();
        return $labs_dentist_joined;
    }
    public function show_account_of_dentist_in_lab($lab_id)
    {
        $show_account_of_dentist_in_lab = $this->LabmangerService->show_account_of_dentist_in_lab($lab_id);
        return $show_account_of_dentist_in_lab;
    }
    public function show_all_labs()
    {
        return $this->LabmangerService->show_all_labs();
    }
    public function show_lab_not_injoied_details($id)
    {
        return $this->LabmangerService->show_lab_not_injoied_details($id);
    }
    public function submit_join_request_to_lab($id)
    {
        return $this->LabmangerService->submit_join_request_to_lab($id);
    }
    public function filter_not_join_labs(Request $request)
    {
        return $this->LabmangerService->filter_not_join_labs($request->province,$request->name);
    }

}
