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

    case OrganizationCreated = 'organization.created';
    case OrganizationUpdated = 'organization.updated';
    case OrganizationDeleted = 'organization.deleted';

    case BlogPostCreated = 'blog_post.created';
    case BlogPostUpdated = 'blog_post.updated';
    case BlogPostPublished = 'blog_post.published';
    case BlogPostDeleted = 'blog_post.deleted';
}
