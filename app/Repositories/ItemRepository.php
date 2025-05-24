<?php
namespace App\Repositories;

use App\Models\Item;
use App\Models\Subcategory;


class ItemRepository
{
    public function getItemsBySubcategory($subcategoryId, $id, $type)
    {
        $subcategory = Subcategory::where('id', $subcategoryId)
            ->with(['items' => function ($query) use ($id, $type) {
                $query->where('creatorable_id', $id)
                    ->where('creatorable_type', $type)
                    ->get();
            }])
            ->first();

        if ($subcategory) {
            return $subcategory->items; // تلقائياً بعد التصفية
        }

        return collect(); // أو [] إذا لم توجد
    }
    public function Verify_permission_to_add_item($subcategoryId,$id,$type)
    {
        $subcategory = Subcategory::where('id', $subcategoryId)->first();

        if ($subcategory) {
            $category = $subcategory->category; // هنا استدعاء العلاقة كـ نموذج، وليس كوظيفة
            if ($category &&
                $category->categoryable_id == $id &&
                $category->categoryable_type == $type){
                    return true;
                }
        }
        return false;
    }
    public function addItem($subcategoryId,$data,$creatorable_id,$creatorable_type)
    {
        $subcategory = Subcategory::where('id', $subcategoryId)->first();
        $category = $subcategory->category;
        if($category){
            $categoryid= $category->id;
        }
        $data['subcategory_id'] = $subcategoryId;
        $data['category_id'] = $categoryid;
        $data['creatorable_id'] = $creatorable_id;
        $data['creatorable_type'] = $creatorable_type;
        $data['quantity'] = 0;
         $res=Item::create($data);
         if ($res) {
         return true;}
         return false;

    }

    public function deleteItem($userid, $type, $id)
    {

        $item = Item::where('id', $id)->first();

        if ($item && $item->creatorable_id == $userid && $item->creatorable_type == $type) {


                $item->delete();
                return true;

        }
        return false;
    }

    public function findItemById($id)
    {
        return Item::find($id);
    }
    public function updateItem($item,$data,$userid,$type)
    {
            if ($item && $item->creatorable_id == $userid && $item->creatorable_type == $type)
                {
                $data['quantity'] = 0;
                $item->update($data);
                return true;
                }

            return false;


    }

}
