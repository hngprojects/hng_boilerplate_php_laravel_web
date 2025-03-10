<?php

namespace App\Policies;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestimonialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any testimonials.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the testimonial.
     */
    public function view(User $user, Testimonial $testimonial)
    {
        return true;
    }

    /**
     * Determine whether the user can create testimonials.
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the testimonial.
     */
    public function update(User $user, Testimonial $testimonial)
    {
        return $user->id === $testimonial->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the testimonial.
     */
    public function delete(User $user, Testimonial $testimonial)
    {
        return $user->role === 'admin';
    }
}