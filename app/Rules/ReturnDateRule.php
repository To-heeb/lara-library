<?php

namespace App\Rules;

use App\Models\Library;
use Illuminate\Contracts\Validation\Rule;

class ReturnDateRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        $issue_date = date('Y-m-d');

        $library_info = Library::getLibraryDetails();
        $addedDays = intval($library_info->book_issue_duration_in_days);
        $due_date =  date('Y-m-d', strtotime($issue_date . " +  $addedDays days"));

        return $value <= $due_date && $value >= $issue_date;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $issue_date = date('Y-m-d');
        $library_info = Library::getLibraryDetails();
        $addedDays = intval($library_info->book_issue_duration_in_days);
        $due_date = date('Y-m-d', strtotime($issue_date . " +  $addedDays days"));

        return "The :attribute must be between $issue_date and  $due_date";
    }
}
