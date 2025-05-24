import React, { useEffect, useState } from "react";
import { useForm, Controller } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import axios from "axios";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { FileChartColumn } from "lucide-react";

const formSchema = z
  .object({
    ac_amount: z.coerce
      .number()
      .min(1, "AC Amount is required")
      .max(1000000, "Amount must be less than 1,000,000"),
    bank_details: z.string().optional(),
    cheque_number: z.string().optional(),
    cheque_date: z.string().optional(),
    payment_mode: z.string().min(1, "Payment Mode field is required"),
    upi_number: z.string().max(50, "UTR number must not exceed 50 characters."),
  })
  .superRefine((data, ctx) => {
    if (data.payment_mode === "UPI") {
      if (!data.upi_number || data.upi_number.trim() === "") {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ["upi_number"],
          message: "UTR number is required when payment mode is UPI",
        });
      }
    }

    if (data.payment_mode === "Bank") {
      if (!data.bank_details || data.bank_details.trim() === "") {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ["bank_details"],
          message: "Bank details are required when payment mode is Bank",
        });
      }
      if (!data.cheque_number || data.cheque_number.trim() === "") {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ["cheque_number"],
          message: "Cheque number is required when payment mode is Bank",
        });
      }
      if (!data.cheque_date || data.cheque_date.trim() === "") {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ["cheque_date"],
          message: "Cheque date is required when payment mode is Bank",
        });
      }
    }
  });

const AddAcCharges = ({ id }) => {
  const [isLoading, setIsLoading] = useState(false);
  const [isDialogOpen, setIsDialogOpen] = useState(false); // State to control dialog visibility

  const queryClient = useQueryClient();
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;

  const defaultValues = {
    ac_amount: "",
    payment_mode: "",
    bank_details: "",
    upi_number: "",
    cheque_number: "",
    cheque_date: "",
  };

  const {
    control,
    handleSubmit,
    watch,
    setValue,
    reset,
    formState: { errors },
    setError,
  } = useForm({
    resolver: zodResolver(formSchema),
    defaultValues,
  });

  const storeMutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.post(`/api/ac_charges/${id}`, data, {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      return response.data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries("ac_charges");
      toast.success("AC Charges Added Successfully");
      setIsLoading(false);
      reset();
      setIsDialogOpen(false); // Close dialog
    },
    onError: (error) => {
      setIsLoading(false);
      if (error.response && error.response.data.errors) {
        const serverErrors = error.response.data.errors;
        if (serverErrors.ac_amount) {
          setError("ac_amount", {
            type: "manual",
            message: serverErrors.ac_amount[0],
          });
        }
      } else {
        toast.error("Failed to add AC Charges.");
      }
    },
  });

  useEffect(() => {
    const paymentMode = watch("payment_mode");

    // Reset fields based on selected paymentMode
    if (paymentMode === "Cash") {
      setValue("upi_number", "");
      setValue("cheque_date", "");
      setValue("cheque_number", "");
      setValue("bank_details", "");
    } else if (paymentMode === "UPI") {
      setValue("cheque_date", "");
      setValue("cheque_number", "");
      setValue("bank_details", "");
    } else if (paymentMode === "Bank") {
      setValue("upi_number", "");
    } else if (paymentMode === "Card") {
      setValue("upi_number", "");
      setValue("cheque_date", "");
      setValue("cheque_number", "");
      setValue("bank_details", "");
    }
  }, [watch("payment_mode"), setValue]);

  const onSubmit = (data) => {
    setIsLoading(true);
    storeMutation.mutate(data);
  };

  return (
    <div>
      <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
        <DialogTrigger asChild>
          <Badge className="bg-blue-600 text-white cursor-pointer hover:bg-blue-800">
            AC Charges
          </Badge>
        </DialogTrigger>
        <DialogContent className="w-[60vw] max-w-[60vw]">
          <DialogHeader>
            <DialogTitle>Add AC Charges</DialogTitle>
            <DialogDescription>Click save when you're done.</DialogDescription>
          </DialogHeader>
          <form onSubmit={handleSubmit(onSubmit)}>
            <div className="grid gap-4 py-4">
              <div className="grid grid-cols-2 items-center gap-4">
                <div className="">
                  <Label className="font-normal" htmlFor="payment_mode">
                    Payment Mode:<span className="text-red-500">*</span>
                  </Label>
                  <Controller
                    name="payment_mode"
                    control={control}
                    render={({ field }) => (
                      <Select
                        value={field.value}
                        onValueChange={(value) => {
                          field.onChange(value);
                        }}
                      >
                        <SelectTrigger className="mt-1">
                          <SelectValue placeholder="Select payent mode" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectGroup>
                            <SelectLabel>Select payment mode</SelectLabel>
                            <SelectItem value="Cash">Cash</SelectItem>
                            <SelectItem value="UPI">UPI</SelectItem>
                            <SelectItem value="Bank">Bank</SelectItem>
                            <SelectItem value="Card">Card</SelectItem>
                          </SelectGroup>
                        </SelectContent>
                      </Select>
                    )}
                  />
                  {errors.payment_mode && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.payment_mode.message}
                    </p>
                  )}
                </div>
                <div className="">
                  <Label htmlFor="ac_amount" className="text-right">
                    AC Amount <span className="text-red-500">*</span>
                  </Label>
                  <Controller
                    name="ac_amount"
                    control={control}
                    render={({ field }) => (
                      <Input
                        {...field}
                        id="ac_amount"
                        type="number"
                        placeholder="Enter AC Amount"
                        className="col-span-3"
                      />
                    )}
                  />
                  {errors.ac_amount && (
                    <p className="text-red-500 text-sm col-span-4">
                      {errors.ac_amount.message}
                    </p>
                  )}
                </div>
                {/* Bank Details */}
                {watch("payment_mode") === "Bank" && (
                  <div className="col-span-2">
                    <div className="grid grid-cols-3 gap-4">
                      <div>
                        <Label htmlFor="bank_details">Bank Details</Label>
                        <Controller
                          name="bank_details"
                          control={control}
                          render={({ field }) => (
                            <Input
                              {...field}
                              id="bank_details"
                              placeholder="Enter Bank Details"
                            />
                          )}
                        />
                        {errors.bank_details && (
                          <p className="text-red-500 text-sm">
                            {errors.bank_details.message}
                          </p>
                        )}
                      </div>

                      {/* Cheque Number */}
                      <div>
                        <Label htmlFor="cheque_number">Cheque Number</Label>
                        <Controller
                          name="cheque_number"
                          control={control}
                          render={({ field }) => (
                            <Input
                              {...field}
                              id="cheque_number"
                              placeholder="Enter Cheque Number"
                            />
                          )}
                        />
                        {errors.cheque_number && (
                          <p className="text-red-500 text-sm">
                            {errors.cheque_number.message}
                          </p>
                        )}
                      </div>

                      {/* Cheque Date */}
                      <div>
                        <Label htmlFor="cheque_date">Cheque Date</Label>
                        <Controller
                          name="cheque_date"
                          control={control}
                          render={({ field }) => (
                            <Input {...field} id="cheque_date" type="date" />
                          )}
                        />
                        {errors.cheque_date && (
                          <p className="text-red-500 text-sm">
                            {errors.cheque_date.message}
                          </p>
                        )}
                      </div>
                    </div>
                  </div>
                )}

                {/* UPI Number */}
                {watch("payment_mode") === "UPI" && (
                  <div>
                    <Label htmlFor="upi_number">UTR Number</Label>
                    <Controller
                      name="upi_number"
                      control={control}
                      render={({ field }) => (
                        <Input
                          {...field}
                          id="upi_number"
                          placeholder="Enter UPI Number"
                        />
                      )}
                    />
                    {errors.upi_number && (
                      <p className="text-red-500 text-sm">
                        {errors.upi_number.message}
                      </p>
                    )}
                  </div>
                )}
              </div>
            </div>
            <DialogFooter>
              <Button
                type="button"
                variant="outline"
                onClick={() => setIsDialogOpen(false)} // Close dialog
                className="text-gray-600 hover:bg-gray-100"
              >
                Cancel
              </Button>
              <Button
                type="submit"
                disabled={isLoading}
                className="bg-blue-600 hover:bg-blue-700 text-white hover:text-white text-sm justify-start"
              >
                {isLoading ? "Submitting..." : "Save changes"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
};

export default AddAcCharges;
