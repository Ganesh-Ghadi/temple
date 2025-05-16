import React, { useState } from "react";
import { useForm, Controller } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import axios from "axios";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";
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

const formSchema = z.object({
  ac_amount: z.coerce
    .number()
    .min(1, "AC Amount is required")
    .max(1000000, "Amount must be less than 1,000,000"),
});

const AddAcCharges = ({ id }) => {
  const [isLoading, setIsLoading] = useState(false);
  const [isDialogOpen, setIsDialogOpen] = useState(false); // State to control dialog visibility

  const queryClient = useQueryClient();
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;

  const defaultValues = {
    ac_amount: "",
  };

  const {
    control,
    handleSubmit,
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

  const onSubmit = (data) => {
    setIsLoading(true);
    storeMutation.mutate(data);
  };

  return (
    <div>
      <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
        <DialogTrigger asChild>
          <Button
            variant="ghost"
            size="sm"
            className="w-full text-sm justify-start"
          >
            <FileChartColumn size={16} /> Add AC Charges
          </Button>
        </DialogTrigger>
        <DialogContent className="sm:max-w-[425px]">
          <DialogHeader>
            <DialogTitle>Add AC Charges</DialogTitle>
            <DialogDescription>Click save when you're done.</DialogDescription>
          </DialogHeader>
          <form onSubmit={handleSubmit(onSubmit)}>
            <div className="grid gap-4 py-4">
              <div className="grid grid-cols-4 items-center gap-4">
                <Label htmlFor="ac_amount" className="text-right">
                  AC Amount
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
