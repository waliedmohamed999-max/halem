@php($isAr = app()->getLocale() === 'ar')
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">{{ $isAr ? 'تاريخ الزيارة' : 'Visit date' }}</label>
        <input type="date" class="form-control" name="visit_date" value="{{ old('visit_date', isset($visit?->visit_date) ? optional($visit->visit_date)->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ $isAr ? 'وقت الزيارة' : 'Visit time' }}</label>
        <input type="time" class="form-control" name="visit_time" value="{{ old('visit_time', $visit->visit_time ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ $isAr ? 'الحالة' : 'Status' }}</label>
        <select class="form-select" name="visit_status" required>
            @foreach(['new', 'follow_up', 'completed', 'canceled'] as $status)
                <option value="{{ $status }}" @selected(old('visit_status', $visit->visit_status ?? 'new') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ $isAr ? 'موعد حجز مرتبط' : 'Related appointment' }}</label>
        <select class="form-select" name="appointment_id">
            <option value="">{{ $isAr ? 'بدون' : 'None' }}</option>
            @foreach($appointments as $appointment)
                <option value="{{ $appointment->id }}" @selected((string) old('appointment_id', $visit->appointment_id ?? '') === (string) $appointment->id)>
                    #{{ $appointment->id }} - {{ $appointment->preferred_date }} {{ $appointment->preferred_time }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label>
        <select class="form-select" name="branch_id">
            <option value="">{{ $isAr ? 'غير محدد' : 'Not set' }}</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $visit->branch_id ?? '') === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ $isAr ? 'الطبيب' : 'Doctor' }}</label>
        <select class="form-select" name="doctor_id">
            <option value="">{{ $isAr ? 'غير محدد' : 'Not set' }}</option>
            @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('doctor_id', $visit->doctor_id ?? '') === (string) $doctor->id)>{{ $doctor->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ $isAr ? 'الشكوى الرئيسية' : 'Chief complaint' }}</label>
        <textarea class="form-control" name="chief_complaint" rows="3">{{ old('chief_complaint', $visit->chief_complaint ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ $isAr ? 'الفحص السريري' : 'Clinical findings' }}</label>
        <textarea class="form-control" name="clinical_findings" rows="3">{{ old('clinical_findings', $visit->clinical_findings ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ $isAr ? 'التشخيص' : 'Diagnosis' }}</label>
        <textarea class="form-control" name="diagnosis" rows="3">{{ old('diagnosis', $visit->diagnosis ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ $isAr ? 'خطة العلاج' : 'Treatment plan' }}</label>
        <textarea class="form-control" name="treatment_plan" rows="3">{{ old('treatment_plan', $visit->treatment_plan ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ $isAr ? 'الإجراء المنفذ' : 'Procedure done' }}</label>
        <textarea class="form-control" name="procedure_done" rows="3">{{ old('procedure_done', $visit->procedure_done ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ $isAr ? 'الوصفة الطبية' : 'Prescription' }}</label>
        <textarea class="form-control" name="prescription" rows="3">{{ old('prescription', $visit->prescription ?? '') }}</textarea>
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ $isAr ? 'موعد المتابعة' : 'Next visit date' }}</label>
        <input type="date" class="form-control" name="next_visit_date" value="{{ old('next_visit_date', isset($visit?->next_visit_date) ? optional($visit->next_visit_date)->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-8">
        <label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label>
        <textarea class="form-control" name="notes" rows="2">{{ old('notes', $visit->notes ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">{{ $isAr ? 'مرفقات الزيارة (أشعة / تحليل / PDF)' : 'Visit attachments (X-ray / Analysis / PDF)' }}</label>
        <input type="file" class="form-control" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf">
        <small class="text-muted">{{ $isAr ? 'الحد الأقصى 10 ملفات، كل ملف حتى 10MB' : 'Max 10 files, each up to 10MB' }}</small>
    </div>

    @if(isset($visit) && $visit->attachments->isNotEmpty())
        <div class="col-12">
            <div class="card card-body">
                <strong class="mb-2">{{ $isAr ? 'المرفقات الحالية' : 'Current Attachments' }}</strong>
                <div class="row g-2">
                    @foreach($visit->attachments as $attachment)
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center border rounded p-2">
                                <a target="_blank" href="{{ asset('storage/' . ltrim($attachment->file_path, '/')) }}">{{ $attachment->file_name }}</a>
                                <label class="form-check-label ms-2">
                                    <input type="checkbox" class="form-check-input me-1" name="delete_attachment_ids[]" value="{{ $attachment->id }}">
                                    {{ $isAr ? 'حذف' : 'Delete' }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
