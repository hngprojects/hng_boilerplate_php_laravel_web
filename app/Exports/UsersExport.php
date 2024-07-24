<?php


// namespace App\Exports;

// use App\Models\User;
// use Maatwebsite\Excel\Concerns\FromCollection;

// class UsersExport implements FromCollection
// {
//     /**
//     * @return \Illuminate\Support\Collection
//     */
//     public function collection()
//     {
//         return User::all();
//     }
// }
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        return collect([$this->user]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Role',
            'Email Verified At',
            'Is Active',
            'Signup Type',
            'Created At',
            'Profile First Name',
            'Profile Last Name',
            'Profile Phone',
            'Profile Avatar URL',
            'Products',
            'Organizations',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone,
            $user->role,
            $user->email_verified_at,
            $user->is_active,
            $user->signup_type,
            $user->created_at,
            $user->profile->first_name,
            $user->profile->last_name,
            $user->profile->phone,
            $user->profile->avatar_url,
            $user->products->pluck('name')->implode(', '),
            $user->organisations->pluck('name')->implode(', '),
        ];
    }
}
