<?php

namespace App\Enums;

enum AuditAction: string
{
    case UserCreated = 'user.created';
    case UserUpdated = 'user.updated';
    case UserRoleChanged = 'user.role_changed';
    case UserDeleted = 'user.deleted';
    case UserProUpgrade = 'user.pro_upgrade';
    case UserExported = 'user.exported';
    case UserAnonymized = 'user.anonymized';
    case UserTwoFactorEnabled = 'user.2fa_enabled';
    case UserTwoFactorConfirmed = 'user.2fa_confirmed';
    case UserTwoFactorDisabled = 'user.2fa_disabled';

    case OrganizationCreated = 'organization.created';
    case OrganizationUpdated = 'organization.updated';
    case OrganizationDeleted = 'organization.deleted';

    case BlogPostCreated = 'blog_post.created';
    case BlogPostUpdated = 'blog_post.updated';
    case BlogPostPublished = 'blog_post.published';
    case BlogPostDeleted = 'blog_post.deleted';

    case AcademyCourseCreated = 'academy_course.created';
    case AcademyCourseUpdated = 'academy_course.updated';
    case AcademyCourseDeleted = 'academy_course.deleted';

    case QuestionnaireCreated = 'questionnaire.created';
    case QuestionnaireUpdated = 'questionnaire.updated';
    case QuestionnaireActivated = 'questionnaire.activated';
    case QuestionnaireDeactivated = 'questionnaire.deactivated';
    case QuestionnaireDeleted = 'questionnaire.deleted';

    case MediaAssetUploaded = 'media_asset.uploaded';
    case MediaAssetDeleted = 'media_asset.deleted';

    case LoginFailed = 'login.failed';
    case AccessDenied = 'access.denied';
}
