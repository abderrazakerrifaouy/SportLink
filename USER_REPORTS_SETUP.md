# User Reports Feature - Implementation Summary

## Overview
A complete user reporting system has been added to SportLink, allowing authenticated users to report inappropriate content (users, posts, comments, replies) with categorized reasons and optional details.

## What's Been Created

### Backend (Laravel)

#### 1. **ReportController** (`backEnd/src/app/Http/Controllers/Api/ReportController.php`)
   - `store()` - Create new report
   - `myReports()` - Retrieve user's submitted reports
   - `show()` - Get specific report details
   - Includes OpenAPI documentation
   - Prevents duplicate reports from same user

#### 2. **ReportResource** (`backEnd/src/app/Http/Resources/ReportResource.php`)
   - Transforms Report model into API response
   - Includes reporter and reviewer relationships
   - OpenAPI schema definition

#### 3. **API Routes** (in `backEnd/src/routes/api.php`)
   ```
   POST   /reports              - Create report
   GET    /reports/my-reports  - Get user's reports
   GET    /reports/{id}        - Get report details
   ```

### Frontend (Vue 3)

#### 1. **reportService** (`frontEnd/app/src/services/reportService.js`)
   - `createReport(data)` - Submit new report
   - `getReport(id)` - Fetch report details
   - `getMyReports()` - Get user's reports

#### 2. **reportStore** (`frontEnd/app/src/stores/reportStore.js`)
   - Pinia store for state management
   - Actions: fetchMyReports, createReport, getReport, clearError
   - Manages loading, error, and reports state

#### 3. **ReportModal Component** (`frontEnd/app/src/components/ReportModal.vue`)
   - Reusable modal dialog for reporting content
   - Pre-defined report reasons:
     - Inappropriate content
     - Spam
     - Harassment or bullying
     - Hate speech
     - Violence or dangerous behavior
     - Misinformation
     - Intellectual property violation
     - Other
   - Optional detailed explanation field (max 1000 chars)
   - Full error handling and validation

## How to Use

### In Any Vue Component

```vue
<template>
  <!-- Report button in your component -->
  <button @click="showReportModal = true" class="...">
    Report
  </button>

  <!-- Add the modal component -->
  <ReportModal
    :is-open="showReportModal"
    reportable-type="App\\Models\\Post"
    :reportable-id="itemId"
    @close="showReportModal = false"
    @success="onReportSuccess"
  />
</template>

<script setup>
import { ref } from 'vue'
import ReportModal from '@/components/ReportModal.vue'

const showReportModal = ref(false)

const onReportSuccess = () => {
  // Handle successful report submission
  console.log('Report submitted!')
}
</script>
```

### Reportable Model Types

Use the fully-qualified class name:
- **Posts**: `App\\Models\\Post`
- **Comments**: `App\\Models\\Comment`
- **Replies**: `App\\Models\\Reply`
- **Users**: `App\\Models\\User`

## Key Features

✅ **Polymorphic Reporting** - Report any model type
✅ **Duplicate Prevention** - Users can't report same content twice
✅ **Categorized Reasons** - Pre-defined reason list with custom details
✅ **Admin Management** - Existing admin panel endpoints for reviewing reports
✅ **User History** - Users can view their submitted reports
✅ **Error Handling** - Comprehensive validation and error messages
✅ **OpenAPI Documentation** - Fully documented API endpoints

## Database

The `reports` table already exists with:
- Polymorphic relationships (reportable_type, reportable_id)
- Status tracking (pending, resolved, dismissed)
- Admin review tracking (reviewed_by, resolved_at)
- Proper indexing for performance

## Next Steps

To integrate reporting into your components:

1. **Import ReportModal** in your component
2. **Add state** for `showReportModal`
3. **Add button** to trigger the modal
4. **Pass props** with reportable_type and reportable_id
5. **Handle success** with @success event

See `REPORTING_FEATURE_EXAMPLE.vue` for a complete example.

## Admin Interface

Admins can manage reports through:
- `/admin/reports` - View all reports
- `/admin/reports/{id}/resolve` - Resolve/dismiss reports
- `/admin/reports/{id}` - Delete reports

## Example Integration Points

Good places to add report buttons:
- PostCard.vue - Report button in menu
- UserProfile.vue - Report user button
- CommentCard.vue - Report comment button
- UserListItem.vue - Report user button

All components follow the same pattern shown in the example.
