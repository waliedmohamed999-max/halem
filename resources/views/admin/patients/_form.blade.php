@php($isAr = app()->getLocale() === 'ar')

<style>
    .patient-form-section {
        border: 1px solid #dbe8f5;
        border-radius: 12px;
        background: #f8fbff;
        padding: 14px;
    }
    .patient-form-section h6 {
        margin-bottom: 12px;
        color: #11365d;
        font-weight: 800;
    }
</style>

<div class="row g-3">
    <div class="col-12 patient-form-section">
        <h6>{{ $isAr ? 'البيانات الأساسية' : 'Basic Information' }}</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ $isAr ? 'الاسم الكامل' : 'Full name' }}</label>
                <input type="text" class="form-control" name="full_name" value="{{ old('full_name', $patient->full_name ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ $isAr ? 'رقم الهاتف' : 'Phone' }}</label>
                <input type="text" class="form-control" name="phone" value="{{ old('phone', $patient->phone ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ $isAr ? 'النوع' : 'Gender' }}</label>
                <select class="form-select" name="gender">
                    <option value="">{{ $isAr ? 'غير محدد' : 'Not specified' }}</option>
                    <option value="male" @selected(old('gender', $patient->gender ?? '') === 'male')>{{ $isAr ? 'ذكر' : 'Male' }}</option>
                    <option value="female" @selected(old('gender', $patient->gender ?? '') === 'female')>{{ $isAr ? 'أنثى' : 'Female' }}</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" class="form-control" name="email" value="{{ old('email', $patient->email ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'تاريخ الميلاد' : 'Date of birth' }}</label>
                <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', isset($patient?->date_of_birth) ? optional($patient->date_of_birth)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'الرقم القومي' : 'National ID' }}</label>
                <input type="text" class="form-control" name="national_id" value="{{ old('national_id', $patient->national_id ?? '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'المهنة' : 'Occupation' }}</label>
                <input type="text" class="form-control" name="occupation" value="{{ old('occupation', $patient->occupation ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'الحالة الاجتماعية' : 'Marital status' }}</label>
                <select class="form-select" name="marital_status">
                    <option value="">{{ $isAr ? 'غير محدد' : 'Not specified' }}</option>
                    <option value="single" @selected(old('marital_status', $patient->marital_status ?? '') === 'single')>{{ $isAr ? 'أعزب/عزباء' : 'Single' }}</option>
                    <option value="married" @selected(old('marital_status', $patient->marital_status ?? '') === 'married')>{{ $isAr ? 'متزوج/متزوجة' : 'Married' }}</option>
                    <option value="divorced" @selected(old('marital_status', $patient->marital_status ?? '') === 'divorced')>{{ $isAr ? 'مطلق/مطلقة' : 'Divorced' }}</option>
                    <option value="widowed" @selected(old('marital_status', $patient->marital_status ?? '') === 'widowed')>{{ $isAr ? 'أرمل/أرملة' : 'Widowed' }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'فصيلة الدم' : 'Blood type' }}</label>
                <input type="text" class="form-control" name="blood_type" value="{{ old('blood_type', $patient->blood_type ?? '') }}" placeholder="A+">
            </div>
            <div class="col-12">
                <label class="form-label">{{ $isAr ? 'العنوان' : 'Address' }}</label>
                <input type="text" class="form-control" name="address" value="{{ old('address', $patient->address ?? '') }}">
            </div>
        </div>
    </div>

    <div class="col-12 patient-form-section">
        <h6>{{ $isAr ? 'بيانات الطوارئ والتأمين' : 'Emergency & Insurance' }}</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'اسم شخص للطوارئ' : 'Emergency contact name' }}</label>
                <input type="text" class="form-control" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'هاتف الطوارئ' : 'Emergency phone' }}</label>
                <input type="text" class="form-control" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'حالة التدخين' : 'Smoking status' }}</label>
                <select class="form-select" name="smoking_status">
                    <option value="">{{ $isAr ? 'غير محدد' : 'Not specified' }}</option>
                    <option value="non_smoker" @selected(old('smoking_status', $patient->smoking_status ?? '') === 'non_smoker')>{{ $isAr ? 'غير مدخن' : 'Non-smoker' }}</option>
                    <option value="smoker" @selected(old('smoking_status', $patient->smoking_status ?? '') === 'smoker')>{{ $isAr ? 'مدخن' : 'Smoker' }}</option>
                    <option value="former_smoker" @selected(old('smoking_status', $patient->smoking_status ?? '') === 'former_smoker')>{{ $isAr ? 'كان مدخن' : 'Former smoker' }}</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ $isAr ? 'شركة التأمين' : 'Insurance company' }}</label>
                <input type="text" class="form-control" name="insurance_company" value="{{ old('insurance_company', $patient->insurance_company ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ $isAr ? 'رقم التأمين' : 'Insurance number' }}</label>
                <input type="text" class="form-control" name="insurance_number" value="{{ old('insurance_number', $patient->insurance_number ?? '') }}">
            </div>
        </div>
    </div>

    <div class="col-12 patient-form-section">
        <h6>{{ $isAr ? 'التاريخ المرضي' : 'Medical History' }}</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ $isAr ? 'الحساسية' : 'Allergies' }}</label>
                <textarea class="form-control" rows="3" name="allergies">{{ old('allergies', $patient->allergies ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ $isAr ? 'أمراض مزمنة' : 'Chronic diseases' }}</label>
                <textarea class="form-control" rows="3" name="chronic_diseases">{{ old('chronic_diseases', $patient->chronic_diseases ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ $isAr ? 'أدوية حالية' : 'Current medications' }}</label>
                <textarea class="form-control" rows="3" name="current_medications">{{ old('current_medications', $patient->current_medications ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ $isAr ? 'عمليات/إجراءات سابقة' : 'Previous surgeries/procedures' }}</label>
                <textarea class="form-control" rows="3" name="previous_surgeries">{{ old('previous_surgeries', $patient->previous_surgeries ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">{{ $isAr ? 'ملاحظات إضافية' : 'Additional notes' }}</label>
                <textarea class="form-control" rows="3" name="notes">{{ old('notes', $patient->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>
