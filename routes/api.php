<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\NullMiddleware;
use App\Http\Controllers\Api\NPSController;
use App\Http\Controllers\Api\PdfController;
use App\Http\Controllers\Api\BondController;
use App\Http\Controllers\Api\ESOPController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\LandController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\CryptoController;
use App\Http\Controllers\Api\BullionController;
use App\Http\Controllers\Api\CharityController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\GratuityController;
use App\Http\Controllers\Api\HomeLoanController;
use App\Http\Controllers\Api\DebentureController;
use App\Http\Controllers\Api\OtherLoanController;
use App\Http\Controllers\Api\BankLockerController;
use App\Http\Controllers\Api\LitigationController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\MutualFundController;
use App\Http\Controllers\Api\OtherAssetController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\FixDepositeController;
use App\Http\Controllers\Api\ShareDetailController;
use App\Http\Controllers\Api\VehicleLoanController;
use App\Http\Controllers\Api\DematAccountController;
use App\Http\Controllers\Api\DigitalAssetController;
use App\Http\Controllers\Api\PersonalLoanController;
use App\Http\Controllers\Api\BusinessAssetController;
use App\Http\Controllers\Api\LifeInsuranceController;
use App\Http\Controllers\Api\OtherDepositeController;
use App\Http\Controllers\Api\ProvidentFundController;
use App\Http\Controllers\Api\BrokingAccountController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\MotorInsuranceController;
use App\Http\Controllers\Api\OtherInsuranceController;
use App\Http\Controllers\Api\SuperAnnuationController;
use App\Http\Controllers\Api\WillGenerationController;
use App\Http\Controllers\Api\AssetAllocationController;
use App\Http\Controllers\Api\HealthInsuranceController;
use App\Http\Controllers\Api\GeneralInsuranceController;
use App\Http\Controllers\Api\PostSavingSchemeController;
use App\Http\Controllers\Api\CommercialPropertyController;
use App\Http\Controllers\Api\OtherFinancialAssetController;
use App\Http\Controllers\Api\PortfolioManagementController;
use App\Http\Controllers\Api\PostalSavingAccountController;
use App\Http\Controllers\Api\PublicProvidentFundController;
use App\Http\Controllers\Api\ResidentialPropertyController;
use App\Http\Controllers\Api\AlternateInvestmentFundController;
use App\Http\Controllers\Api\WealthManagementAccountController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(['middleware'=>['auth.guest']], function(){
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.reset');

});
// 
Route::group(['middleware'=>['auth:sanctum', 'request.null']], function(){
    Route::resource('profiles', ProfileController::class);
    Route::resource('beneficiaries', BeneficiaryController::class);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::resource('motor-insurances', MotorInsuranceController::class);
    Route::resource('life-insurances', LifeInsuranceController::class);
    Route::resource('other-insurances', OtherInsuranceController::class);
    Route::resource('general-insurances', GeneralInsuranceController::class);
    Route::resource('health-insurances', HealthInsuranceController::class);
    Route::resource('bullions', BullionController::class);
    Route::resource('memberships', MembershipController::class);
    Route::resource('vehicle-loans', VehicleLoanController::class);
    Route::resource('mutual-funds', MutualFundController::class);
    Route::resource('debentures', DebentureController::class);
    Route::resource('home-loans', HomeLoanController::class);
    Route::resource('personal-loans', PersonalLoanController::class);
    Route::resource('other-loans', OtherLoanController::class);
    Route::resource('litigations', LitigationController::class);
    Route::resource('cryptos', CryptoController::class);
    Route::resource('bonds', BondController::class);
    Route::resource('bank-accounts', BankAccountController::class);
    Route::resource('bank-lockers', BankLockerController::class);
    Route::resource('fix-deposits', FixDepositeController::class);
    Route::resource('business-assets', BusinessAssetController::class);
    Route::delete('/logout', [UserController::class, 'logout']);
    Route::resource('other-assets', OtherAssetController::class);
    Route::get('/storage/pan/{filePath}', [ProfileController::class, 'showPanFiles']);
    Route::resource('public-provident-funds', PublicProvidentFundController::class);
    Route::resource('provident-funds', ProvidentFundController::class);
    Route::resource('nps', NPSController::class);
    Route::resource('gratuities', GratuityController::class);
    Route::resource('super-annuations', SuperAnnuationController::class);
    Route::resource('digital-assets', DigitalAssetController::class);
    Route::resource('other-deposites', OtherDepositeController::class);
    Route::resource('post-saving-schemes', PostSavingSchemeController::class);
    Route::resource('post-saving-account-details', PostalSavingAccountController::class);
    Route::resource('lands', LandController::class);
    Route::resource('residential-properties', ResidentialPropertyController::class);
    Route::resource('commercial-properties', CommercialPropertyController::class);
    Route::resource('share-details', ShareDetailController::class);
    Route::resource('esops', ESOPController::class);
    Route::resource('demat-accounts', DematAccountController::class);
    Route::resource('wealth-management-accounts', WealthManagementAccountController::class);
    Route::resource('broking-accounts', BrokingAccountController::class);
    Route::resource('alternate-investment-funds', AlternateInvestmentFundController::class);
    Route::resource('portfolio-managements', PortfolioManagementController::class);
    Route::resource('other-financial-assets', OtherFinancialAssetController::class);
    Route::resource('assets', AssetController::class);
    Route::post('/will/allocate',[AssetAllocationController::class, 'storeMultipleAssets']);
    Route::get('/will/allocate/{asset_id}/{asset_type}/{level}',[AssetAllocationController::class, 'getMultipleRecords']);
    Route::get('/asset-allocations/primary-beneficiaries/{asset_id}/{asset_type}/{level}',[AssetAllocationController::class, 'getPrimaryBeneficiaries']);
    Route::get('/generate-will', [WillGenerationController::class,'generateWill']);
    Route::get('/download-will', [WillGenerationController::class,'downloadWill']);

});
Route::get('/file/{files}', [ProfileController::class, 'showFiles'])->where('files', '.*');
// Route::get('/p/{files}', [ProfileController::class, 'showFiles'])->where('files', '.*');

// Route::get('/generate-will', [WillGenerationController::class,'generateWill']);

// Route::get('/file/{files}', [ProfileController::class, 'showFiles'])->where('files', '.*');

// Route::get('/pan/{files}', [ProfileController::class, 'showPanFiles']);
// Route::get('/passport/{files}', [ProfileController::class, 'showPassportFiles']);;
// Route::get('/driving/{files}', [ProfileController::class, 'showDrivingLicenceFiles']);

// Route::get('/generate-pdf', [PdfController::class,'generatePDF']);
