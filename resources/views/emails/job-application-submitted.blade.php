<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e5e7eb;
        }
        .highlight {
            background-color: #dbeafe;
            padding: 15px;
            border-left: 4px solid #3b82f6;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
        .application-details {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-label {
            font-weight: bold;
            color: #374151;
        }
        .detail-value {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Application Received!</h1>
    </div>

    <div class="content">
        <h2>Dear {{ $applicantName }},</h2>

        <p>Thank you for your interest in the <strong>{{ $jobTitle }}</strong> position at <strong>{{ $company }}</strong>. We have successfully received your job application.</p>

        <div class="highlight">
            <strong>What happens next?</strong><br>
            Our hiring team will review your application and contact you if your qualifications match our requirements. This process typically takes 5-10 business days.
        </div>

        <div class="application-details">
            <h3>Application Details</h3>
            <div class="detail-row">
                <span class="detail-label">Application ID:</span>
                <span class="detail-value">#{{ $applicationId }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Position:</span>
                <span class="detail-value">{{ $jobTitle }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Company:</span>
                <span class="detail-value">{{ $company }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Submission Date:</span>
                <span class="detail-value">{{ $submissionDate }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">Under Review</span>
            </div>
        </div>

        <p><strong>Pro Tip:</strong> Keep this email for your records. You can reference your Application ID (#{{ $applicationId }}) if you need to contact us about your application.</p>

        <p>We appreciate your interest in joining our team and look forward to potentially working with you!</p>

        <p>Best regards,<br>
        <strong>{{ $company }} Hiring Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} {{ $company }}. All rights reserved.</p>
    </div>
</body>
</html>
