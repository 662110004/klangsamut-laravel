<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * 1. หน้า List (พร้อมค้นหา)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                // ค้นหาใน 'name', 'email', หรือ 'role'
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * 2. หน้า Show (แสดงรายละเอียด)
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * 3. ปุ่ม Delete (ลบผู้ใช้)
     */
    public function destroy(User $user)
    {
        // ตรวจสอบว่าใช่ตัวเองหรือไม่
        if (Auth::id() === $user->id) {
            // ถ้าใช่ ห้ามลบ
            return redirect(session()->get('bookmark_url.users', route('admin.users.index')))
                ->with('error', 'You cannot delete your own account!');
        }

        // (ควรเพิ่ม: ตรวจสอบว่า User นี้มีข้อมูลเชื่อมโยงอื่นหรือไม่ เช่น เป็นคนสร้างหนังสือ?)
        // (สำหรับโปรเจกต์นี้ เราจะลบเลย)

        $user->delete();

        return redirect(session()->get('bookmark_url.users', route('admin.users.index')))
            ->with('success', 'User deleted successfully.');
    }

    /**
     * 4. (ใหม่) อัปเดต Role ของ User
     */
    public function updateRole(Request $request, User $user)
    {
        // 1. (สำคัญ!) ป้องกันไม่ให้ Admin เปลี่ยน Role ตัวเอง
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        // 2. ตรวจสอบข้อมูล
        $validatedData = $request->validate([
            // Role ที่ส่งมาต้องเป็น 'user' หรือ 'admin' เท่านั้น
            'role' => ['required', Rule::in(['user', 'admin'])]
        ]);

        // 3. อัปเดต Role
        $user->update([
            'role' => $validatedData['role']
        ]);

        // 4. ส่งกลับไปหน้า Show (หน้านี้) พร้อม Notification
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User role updated successfully.');
    }
}
