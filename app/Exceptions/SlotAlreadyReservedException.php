<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * 予約しようとした教室・日時が、すでに他の予約で埋まっていたことを表す。
 *
 * 重複確認と登録はトランザクション内で行うため、
 * 「重複していたので中断する」ことを呼び出し元に伝える手段として例外を使う。
 * （途中でreturnするとトランザクションが確定してしまうため）
 */
class SlotAlreadyReservedException extends RuntimeException
{
}
