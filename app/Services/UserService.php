<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function __construct(User $user)
    {

    }

    public function list(int $per_page): LengthAwarePaginator
    {
        return User::paginate($per_page ?? 10);
    }

    public function store(array $attribute)
    {
        if( !empty($attribute['photo']) ) {
            $attribute['photo'] = $this->upload($attribute['photo']);
        }
        return User::create($attribute);
    }

    public function find(int $id)
    {
       return User::findOrFail($id);
    }

    public function update(int $id, array $attribute)
    {
        if( !empty($attribute['photo']) ) {
            $attribute['photo'] = $this->upload($attribute['photo']);
        }
        if( empty($attribute['password']) ) {
            unset($attribute['password']);
        }
        $user = User::findOrFail($id);
        return $user->update($attribute);
    }

    public function destroy(int $id)
    {
        $user = User::find($id);
        $user->delete();
    }

    public function listTrashed($per_page): LengthAwarePaginator
    {
        return User::onlyTrashed()->paginate($per_page ?? 10);
    }

    public function restore(int $id)
    {
        $user = User::withTrashed()->find($id);
        if ($user && $user->trashed()) {
            $user->restore();
        }
    }

    public function delete(int $id)
    {
        $user = User::withTrashed()->find($id);
        if ($user && $user->trashed()) {
            $user->forceDelete();
        }
    }

    public function upload(UploadedFile $file)
    {
        $path = $file->store('images', 'public');
        return $path;
    }
}
