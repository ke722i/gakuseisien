<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $question_id
 * @property int $user_id
 * @property string $content
 * @property bool $is_teacher_approved
 * @property int $upvote_count
 * @property \Illuminate\Support\Carbon $created_at
 * @property int|null $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Answer> $replies
 * @property-read int|null $replies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $upvoters
 * @property-read int|null $upvoters_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereIsTeacherApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereUpvoteCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereUserId($value)
 */
	class Answer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $category
 * @property \Illuminate\Support\Carbon $start_at
 * @property \Illuminate\Support\Carbon|null $end_at
 * @property bool $all_day
 * @property string|null $location
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAllDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $content
 * @property string|null $category
 * @property string|null $location
 * @property string|null $image_url
 * @property string|null $posted_by
 * @property \Illuminate\Support\Carbon $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post wherePostedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 */
	class Post extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $school_id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property int|null $best_answer_id
 * @property string $category
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Answer> $answers
 * @property-read int|null $answers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereBestAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUserId($value)
 */
	class Question extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $question_id
 * @property int|null $user_id
 * @property string $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Question $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUserId($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $genre
 * @property string $address
 * @property string $business_hours
 * @property int $budget
 * @property int $distance
 * @property string $payment_method
 * @property bool $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereBusinessHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUpdatedAt($value)
 */
	class Shop extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $genre
 * @property string $address
 * @property string $business_hours
 * @property int $budget
 * @property int $distance
 * @property string $payment_method
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereBusinessHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereUpdatedAt($value)
 */
	class ShopRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $user_id
 * @property int $answer_id
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upvote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upvote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upvote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upvote whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upvote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Upvote whereUserId($value)
 */
	class Upvote extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $login_id
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $student_number
 * @property string|null $class_number
 * @property string|null $student_name
 * @property string|null $homeroom_teacher
 * @property string|null $teacher_number
 * @property string|null $teacher_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Answer> $upvotedAnswers
 * @property-read int|null $upvoted_answers_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereClassNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereHomeroomTeacher($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLoginId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStudentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStudentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTeacherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTeacherNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

