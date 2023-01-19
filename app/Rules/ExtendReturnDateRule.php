<?php

namespace App\Rules;

use App\Models\Library;
use App\Models\BookIssue;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

class ExtendReturnDateRule implements Rule, DataAwareRule
{



    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data = [];

    protected  $pending_book_due_date;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // ...

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
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
        $pending_book = BookIssue::where([
            ["user_id", "=", $this->data['user_id']],
            ["book_id", "=", $this->data['book_id']]
        ])->first();

        $this->pending_book_due_date = $pending_book->due_date;
        $library_info = Library::getLibraryDetails();
        $addedDays = intval($library_info->book_issue_duration_in_days);
        $due_date =  date('Y-m-d', strtotime($pending_book->due_date . " +  $addedDays days"));


        return $value <= $due_date && $value >= date('Y-m-d');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $issue_date =  $this->pending_book_due_date;
        $library_info = Library::getLibraryDetails();
        $addedDays = intval($library_info->book_issue_duration_in_days);
        $due_date = date('Y-m-d', strtotime($issue_date . " +  $addedDays days"));
        $today = date('Y-m-d');

        return "The :attribute must be between $today and  $due_date";
    }
}
