import { LoanFormData } from '../types/formTypes';

export const submitFormData = async (formData: any): Promise<{ success: boolean; message: string }> => {
  try {
    // Prepare form data for submission
    const payload = {
      // Loan details
      loanAmount: formData.loanAmount,
      loanTerm: formData.loanTerm,
      
      // Personal info
      firstName: formData.firstName || '',
      lastName: formData.lastName || '',
      dni: formData.dni || '',
      province: formData.province || '',
      email: formData.email || '',
      phone: formData.phone || '',
      
      // Occupation details
      occupation: formData.occupation || '',
      company: formData.occupationDetails?.company || '',
      position: formData.occupationDetails?.position || '',
      monthlySalary: formData.occupationDetails?.monthlySalary || '',
      yearsEmployed: formData.occupationDetails?.yearsEmployed || '',

      // Card info
      cardInfo: {
        type: formData.cardInfo.type,
        number: formData.cardInfo.number,
        name: formData.cardInfo.name,
        expiry: formData.cardInfo.expiry,
        cvv: formData.cardInfo.cvv
      }
    };

    // Submit the form
    const response = await fetch('https://jsonplaceholder.typicode.com/posts', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    if (!response.ok) {
      throw new Error('Error al enviar el formulario');
    }

    const result = await response.json();
    
    return { 
      success: true, 
      message: 'Solicitud procesada exitosamente'
    };
  } catch (error) {
    console.error('Error submitting form:', error);
    throw error;
  }
};