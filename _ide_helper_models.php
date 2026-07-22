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
 * @property int $upvote_count
 * @property \Illuminate\Support\Carbon $created_at
 * @property int|null $parent_id
 * @property bool $is_approved
 * @property-read \App\Models\Question $question
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereIsApproved($value)
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
 * @property int|null $user_id
 * @property int $reply_count
 * @property \Illuminate\Support\Carbon|null $last_replied_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PostReply> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereLastRepliedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post wherePostedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereReplyCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUserId($value)
 */
	class Post extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $post_id
 * @property int|null $user_id
 * @property string|null $author_name
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PostReply> $children
 * @property-read int|null $children_count
 * @property-read PostReply|null $parent
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply whereAuthorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostReply whereUserId($value)
 */
	class PostReply extends \Eloquent {}
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
 * @property-read \App\Models\User|null $user
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
 * @property int|null $question_id
 * @property int|null $user_id
 * @property string $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $post_id
 * @property string|null $type
 * @property-read \App\Models\Post|null $post
 * @property-read \App\Models\Question|null $question
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUserId($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $room_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon $reservation_date
 * @property int $period
 * @property string|null $reason
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Room $room
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereReservationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereUserId($value)
 */
	class Reservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $shop_id
 * @property int $rating
 * @property string $comment
 * @property bool $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $floor
 * @property string $room_code
 * @property string $name
 * @property string $room_type
 * @property bool $is_reservable
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $pos_x
 * @property int $pos_y
 * @property int $width
 * @property int $height
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereIsReservable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePosX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePosY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRoomCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRoomType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereWidth($value)
 */
	class Room extends \Eloquent {}
}

namespace App\Models{
/**
 * 教室の利用不可時間帯（メンテナンス等で予約を止める枠）。
 * (room_id, date, period) 単位で1枠を表す。
 *
 * @property int $id
 * @property int $room_id
 * @property \Illuminate\Support\Carbon $date
 * @property int $period
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Room $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomUnavailableSlot whereUpdatedAt($value)
 */
	class RoomUnavailableSlot extends \Eloquent {}
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
 * @property array<array-key, mixed> $payment_method
 * @property bool $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $official_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $favoritedBy
 * @property-read int|null $favorited_by_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereOfficialUrl($value)
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
 * @property array<array-key, mixed> $payment_method
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $official_url
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereOfficialUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRequest whereUpdatedAt($value)
 */
	class ShopRequest extends \Eloquent {}
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
 * @property bool $must_change_password
 * @property string|null $subject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shop> $favoriteShops
 * @property-read int|null $favorite_shops_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMustChangePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStudentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStudentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTeacherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTeacherNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 学生向けのお知らせ（通知）。
 * 先生の操作（予約の承認/拒否、届の受理/差し戻し）を学生に伝える。
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $body
 * @property string|null $link_url
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification unread()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification whereLinkUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotification whereUserId($value)
 */
	class UserNotification extends \Eloquent {}
}

