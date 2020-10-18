<?php

use yii\helpers\Url;
use kartik\widgets\FileInput;

/**
* @var yii\web\View $this
* @var common\models\Bug $model
*/

$previews = $model->getDocumentPreviews();
?>

<div>
    <?= FileInput::widget([
        'name' => 'BugCreationForm[documents][]',
        'options' => [
         'multiple' => true
        ],
        'pluginOptions' => [
         'allowedFileExtensions' => [
             'jpg', 'jpeg', 'png', 'txt', 'csv', 'pdf', 'json'
         ],
         'uploadUrl' => Url::to('/bug/upload-file'),
         'uploadExtraData' => [
             'immediate' => true,
             'bug_id' => $model->id
         ],
         'maxFileCount' => 5,
         'showCancel' => false,
         'showCaption' => false,
         'showRemove' => false,
         'showUpload' => false,
         'showClose' => false,
         'browseLabel' => 'Add Document',
         'dropZoneEnabled' => false,
         'initialPreview' => $previews['data'],
         'initialPreviewConfig'  => $previews['config'],
         'initialPreviewAsData' => true,
         'overwriteInitial' => false,
         'deleteUrl' => Url::to('/bug/remove-file'),
         // Placeholder: downloads not yet implemented
         'initialPreviewDownloadUrl' => Url::to('/bug/download-file'),
         'msgUploadEmpty' => 'File already uploaded',
        ],
        'pluginEvents' => [
            'filebatchselected' => "function(event, files) {
                  $(this).fileinput('upload');
             }",
            'filepreremove' => "function(event, id, index) {
                removeFile($model->id, event, id, index);
            }",
            'filepredelete' => "function(event) {
                return !confirm(`Are you sure you want to delete this file?`);
            }",
        ],
    ]) ?>
</div>

<script type="text/javascript">
    function removeFile(bugId, event, fileId, thumbId) {
        let error = uploadHasError(thumbId);
        if (error || confirm(`Are you sure you want to delete this file?`)) {
            $.ajax({
                type: 'POST',
                url: '/bug/remove-file',
                data: {
                    filename: fileId.split('_').pop(),
                    immediate: true,
                    bug_id: `${bugId}`,
                    has_error: uploadHasError(thumbId),
                }
            });
        } else {
            event.preventDefault();
        }
    }

    function uploadHasError(thumbId) {
        return $(`[data-fileid='${thumbId}']`).hasClass('file-preview-error');
    }
</script>
