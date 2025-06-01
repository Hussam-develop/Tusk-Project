<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getCategoriesForCurrentUser()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass(); // نوع المستخدم، مثلاً App\Models\Admin


        return Category::where('categoryable_id', $user->id)
            ->where('categoryable_type', $type)
            ->get(['id', 'name']);
    }
    public function create($id, $type, array $data)
    {
        $data['categoryable_id'] = $id;
        $data['categoryable_type'] = $type;

        return Category::create($data);
    }
    public function deleteCategory($userid, $type, $id)
    {

        $category = Category::find($id);



        $category = Category::find($id);

        if ($category && $category->categoryable_id == $userid && $category->categoryable_type == $type) {
            $category->delete();
            $category->subcategories()->delete();
            return true;
        }

        return false;
    }

    public function findCategoryById($id)
    {
        return Category::find($id);
    }

    public function updateCategory(Category $category, array $data)
    {
        return $category->update($data);
    }
}
