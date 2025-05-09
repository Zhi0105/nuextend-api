<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
class PDFController extends Controller
{
    public function pdf003(Request $request) {
        try {

            return response()->json([
                "status" => 200,
                "message" => "walang pdf mahirap i fill out qpal nameng prof"
            ], 200);
            // $data = $request->all();

            // $pdf = new Fpdi();
            // $pdf->AddPage();
            // $pdf->setSourceFile(storage_path('app/003.pdf')); // Your existing PDF
            // $templateId = $pdf->importPage(1);
            // $pdf->useTemplate($templateId);

            // $pdf->SetFont('Helvetica');
            // $pdf->SetTextColor(0, 0, 0);

            // // Example positions - adjust as needed
            // $pdf->SetXY(50, 47);
            // $pdf->Write(10, $data['title']);

            // // $pdf->SetXY(50, 60);
            // // $pdf->Write(10, $data['email']);

            // $outputPath = storage_path('app/generated003.pdf');
            // $pdf->Output($outputPath, 'F');

            // return response()
            // ->download($outputPath)
            // ->deleteFileAfterSend(false)
            // // ->headers->set('X-Message', 'PDF generated successfully')
            // ->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  400,
                'message' => $e->getMessage(),
            ],  400);
        }
    }
}
