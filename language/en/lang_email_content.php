<?php

/*
#======================================================
|    | Trellis Desk Language File
|    | lang_email_content.php
#======================================================
*/

$lang['header'] = <<<EOF
Dear <#MEM_NAME#>,
EOF;

$lang['footer'] = <<<EOF
Regards,

The <#HD_NAME#> team.

<#HD_URL#>
EOF;

$lang['change_email_val_sub'] = "Verify Your Email";

$lang['change_email_val'] = <<<EOF
You have requested for your email to be changed to this address.  In order to complete the change, you must verify this email by clicking the validation link below.

---------------------------

<#VAL_LINK#>

---------------------------
EOF;

$lang['new_user_val_email_sub'] = "Verify Your New Account";

$lang['new_user_val_email'] = <<<EOF
Welcome to <#HD_NAME#>.  You have requested a new account at our help desk system.  In order to activate your account, you must verify this email address by clicking the validation link below.

---------------------------

Username: <#USER_NAME#>
Validation Link: <#VAL_LINK#>

---------------------------
EOF;

$lang['new_user_val_both_sub'] = "Verify Your New Account";

$lang['new_user_val_both'] = <<<EOF
Welcome to <#HD_NAME#>.  You have requested a new account at our help desk system.  In order to activate your account, you must verify this email address by clicking the validation link below.  Additionally, an administrator must manually approve your account.

---------------------------

Username: <#USER_NAME#>
Validation Link: <#VAL_LINK#>

---------------------------

Remember, in additional to verifying your email address, an administrator must also manually approve your account.  An email will be sent to notify you when your account is approved.
EOF;

$lang['new_user_val_admin_sub'] = "Your New Account";

$lang['new_user_val_admin'] = <<<EOF
You have requested a new account at our help desk system.  Before you can begin using your account, an administrator must manually approve your account.  You will receive an email when your account is approved.
EOF;

$lang['acc_accivated_sub'] = "Account Activated";

$lang['acc_accivated'] = <<<EOF
Your account has been successfully activated.  You may now login.
EOF;

$lang['acc_almost_accivated_sub'] = "Account Waiting Approval";

$lang['acc_almost_accivated'] = <<<EOF
Your email address has been successfully verified.  But before you can begin using your account, an administrator must manually approve your account.  You will receive an email when your account is approved.
EOF;

$lang['acc_approved_sub'] = "Account Approved";

$lang['acc_approved'] = <<<EOF
Your account has been successfully approved by an administrator.  You may now login.
EOF;

$lang['acc_almost_approved_sub'] = "Account Waiting Email Validation";

$lang['acc_almost_approved'] = <<<EOF
Your account has been successfully approved by an administrator.  But before you can begin using your account, you must first click the validation link in the email that has been dispatched to your email address.
EOF;

$lang['new_ticket_sub'] = "Ticket ID #<#TICKET_ID#>";

$lang['new_ticket'] = <<<EOF
You have submitted a new ticket.  Our staff will review the ticket shortly and reply accordingly.  Below are the ticket details.

---------------------------

Ticket ID: <#TICKET_ID#>
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

You can view your ticket using this link: <#TICKET_LINK#>
EOF;

$lang['staff_new_ticket_sub'] = "Ticket ID #<#TICKET_ID#>";

$lang['staff_new_ticket'] = <<<EOF
A new ticket has been created in your department.  Below are the ticket details.

---------------------------

Ticket ID: <#TICKET_ID#>
Member: <#MEMBER#>
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

<#MESSAGE#>

---------------------------

You can manage this ticket using this link: <#TICKET_LINK#>
EOF;

$lang['new_guest_ticket_sub'] = "Ticket ID #<#TICKET_ID#>";

$lang['new_guest_ticket'] = <<<EOF
You have submitted a new guest ticket.  Our staff will review the ticket shortly and reply accordingly.  Below are the ticket details.

---------------------------

Ticket ID: <#TICKET_ID#>
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

Ticket Key: <#TICKET_KEY#>

---------------------------

<#MESSAGE#>

---------------------------

You can view your ticket using this link: <#TICKET_LINK#>
EOF;

$lang['staff_new_guest_ticket_sub'] = "Ticket ID #<#TICKET_ID#>";

$lang['staff_new_guest_ticket'] = <<<EOF
A new guest ticket has been created in your department.  Below are the ticket details.

---------------------------

Ticket ID: <#TICKET_ID#>
Member: <#MEMBER#> (Guest)
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

<#MESSAGE#>

---------------------------

You can manage this ticket using this link: <#TICKET_LINK#>
EOF;

$lang['ticket_escl_sub'] = "Ticket ID #<#TICKET_ID#> Escalated";

$lang['ticket_escl'] = <<<EOF
One of your tickets has been escalated.  Our managers will be reviewing your ticket shortly.  Below are the ticket details.

---------------------------

Ticket ID: <#TICKET_ID#>
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

You can view your ticket using this link: <#TICKET_LINK#>
EOF;

$lang['ticket_close_sub'] = "Ticket ID #<#TICKET_ID#> Closed";

$lang['ticket_close'] = <<<EOF
One of your tickets has been closed.  Below are the ticket details.

---------------------------

Ticket ID: <#TICKET_ID#>
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

You can view your ticket using this link: <#TICKET_LINK#>

If there is anything we can do to be of assistance, please let us know.
EOF;

$lang['ticket_move_sub'] = "Ticket ID #<#TICKET_ID#> Moved";

$lang['ticket_move'] = <<<EOF
One of your tickets has been moved to a new department.  Below are the ticket details.

---------------------------

Ticket ID: <#TICKET_ID#>
Subject: <#SUBJECT#>
Old Department: <#OLD_DEPARTMENT#>
New Department: <#NEW_DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

You can view your ticket using this link: <#TICKET_LINK#>

If there is anything we can do to be of assistance, please let us know.
EOF;

$lang['ticket_reply_sub'] = "Ticket ID #<#TICKET_ID#> Reply";

$lang['ticket_reply'] = <<<EOF
A reply has been made to your ticket.  Below are the ticket details.

---------------------------

<#REPLY#>

---------------------------

Ticket ID: <#TICKET_ID#>
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

You can view your ticket using this link: <#TICKET_LINK#>

If there is anything we can do to be of assistance, please let us know.
EOF;

$lang['ticket_reply_guest_sub'] = "Ticket ID #<#TICKET_ID#> Reply";

$lang['ticket_reply_guest'] = <<<EOF
A reply has been made to your ticket.  Below are the ticket details.

---------------------------

<#REPLY#>

---------------------------

Ticket ID: <#TICKET_ID#>
Ticket Key: <#TICKET_KEY#>
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

You can view your ticket using this link: <#TICKET_LINK#>

If there is anything we can do to be of assistance, please let us know.
EOF;

$lang['staff_reply_ticket_sub'] = "Ticket ID #<#TICKET_ID#> Reply";

$lang['staff_reply_ticket'] = <<<EOF
A reply has been made to a ticket in your department.  Below are the ticket details.

---------------------------

<#REPLY#>

---------------------------

Ticket ID: <#TICKET_ID#>
Member: <#MEMBER#>
Subject: <#SUBJECT#>
Department: <#DEPARTMENT#>
Priority: <#PRIORITY#>
Date Submitted: <#SUB_DATE#>

---------------------------

You can manage this ticket using this link: <#TICKET_LINK#>
EOF;

$lang['announcement_sub'] = "<#TITLE#>";

$lang['announcement'] = <<<EOF
A new announcement has been made titled '<#TITLE#>'.

---------------------------

<#CONTENT#>

---------------------------

You have received this email because you selected to receive email notifications for new announcements in your profile.  If you would like to discontinue these emails, login and update your account preferences.
EOF;

$lang['reset_pass_val_sub'] = "Reset Your Password";

$lang['reset_pass_val'] = <<<EOF
You have requested to reset your password at <#HD_NAME#>.  In order to reset your password, click the validation link below.  If you did not request to reset your password, please disregard this email.

---------------------------

Username: <#USER_NAME#>
Validation Link: <#VAL_LINK#>
EOF;

$lang['reply_pipe_closed_sub'] = "Reply Not Accepted";

$lang['reply_pipe_closed'] = <<<EOF
We were unable to accept your email and add your reply because the ticket is closed.

---------------------------

Ticket ID: <#TICKET_ID#>
Subject: <#SUBJECT#>

---------------------------

If you feel this message is an error, please contact an administrator.
EOF;

$lang['ticket_pipe_rejected_sub'] = "Ticket Not Accepted";

$lang['ticket_pipe_rejected'] = <<<EOF
We were unable to accept your email and create a ticket because you do not have permission to create tickets in this department.

If you feel this message is an error, please contact an administrator.
EOF;

$lang['new_user_admin_val_sub'] = "New Registration: <#USER_NAME#>";

$lang['new_user_admin_val'] = <<<EOF
A new member has registered as is awaiting admin validation.  Below are the member details.

---------------------------

Member: <#USER_NAME#>
Email: <#USER_EMAIL#>
Date Joined: <#JOIN_DATE#>

---------------------------

You can manage members awaiting validation using this link: <#APPROVE_LINK#>
EOF;

?>