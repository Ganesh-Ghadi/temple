import React, { useEffect, useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm, Controller } from "react-hook-form";
import { z } from "zod";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Loader2 } from "lucide-react";

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import axios from "axios";
import { Button } from "@/components/ui/button";
import { useNavigate, useParams } from "react-router-dom";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";

const formSchema = z.object({
  receipt_type_id: z.coerce.number().min(1, "Receipt Type field is required"),
  receipt_date: z.string().min(1, "receipt date field is required"),
  name: z.string().min(2, "name field must have at least 2 characters"),
  receipt_head: z.string().min(2, "receipt head field is required"),
  gotra: z.string().min(2, "gotra field must have at least 2 characters"),
  amount: z.coerce.number().min(1, "amount filed is required"),
});

const Update = () => {
  const [isLoading, setIsLoading] = useState(false);
  const queryClient = useQueryClient();
  const { id } = useParams();
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;
  const navigate = useNavigate();

  const defaultValues = {
    receipt_type_id: "",
    receipt_date: "",
    name: "",
    gotra: "",
    amount: "",
    receipt_head: "",
  };

  const {
    control,
    handleSubmit,
    formState: { errors },
    setValue,
    setError,
  } = useForm({ resolver: zodResolver(formSchema), defaultValues });

  const {
    data: allReceiptTypesData,
    isLoading: isAllReceiptTypesDataLoading,
    isError: isAllReceiptTypesDataError,
  } = useQuery({
    queryKey: ["allReceiptTypes"], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(`/api/all_receipt_types`, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  const {
    data: allReceiptHeadsData,
    isLoading: isAllReceiptHeadsDataLoading,
    isError: isAllReceiptHeadsDataError,
  } = useQuery({
    queryKey: ["allReceiptHeads"], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(`/api/all_receipt_heads`, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  const {
    data: editReceipt,
    isLoading: isEditReceiptDataLoading,
    isError: isEditReceiptDataError,
  } = useQuery({
    queryKey: ["editReceipt", id], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(`/api/receipts/${id}`, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  useEffect(() => {
    if (editReceipt) {
      setValue("receipt_type_id", editReceipt.Receipt?.receipt_type_id);
      setValue("receipt_date", editReceipt.Receipt?.receipt_date);
      setValue("name", editReceipt.Receipt?.name);
      setValue("gotra", editReceipt.Receipt?.gotra);
      setValue("amount", editReceipt.Receipt?.amount);
      setValue("receipt_head", editReceipt.Receipt?.receipt_head);
    }
  }, [editReceipt, setValue]);

  const updateMutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.put(`/api/receipts/${id}`, data, {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`, // Include the Bearer token
        },
      });
      return response.data;
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries("receipts");

      toast.success("Receipts Updated Successfully");
      setIsLoading(false);
      navigate("/receipts");
    },
    onError: (error) => {
      setIsLoading(false);
      toast.error("Failed to update Receipt");
      console.log("got error ", error);
    },
  });
  const onSubmit = (data) => {
    console.log("Clicked");
    setIsLoading(true);
    updateMutation.mutate(data);
  };

  return (
    <>
      <div className="p-5">
        {/* breadcrumb start */}
        <div className=" mb-7 text-sm">
          <div className="flex items-center space-x-2 text-gray-700">
            <span className="">
              <Button
                onClick={() => navigate("/receipts")}
                className="p-0 text-blue-700 text-sm font-light"
                variant="link"
              >
                Receipts
              </Button>
            </span>
            <span className="text-gray-400">/</span>
            <span className="dark:text-gray-300">Add</span>
          </div>
        </div>
        {/* breadcrumb ends */}

        {/* form style strat */}
        <form onSubmit={handleSubmit(onSubmit)}>
          <div className="px-5 pb-4 mb-5 dark:bg-background pt-1 w-full bg-white shadow-lg border  rounded-md">
            <div className="w-full py-3 flex justify-start items-center">
              <h2 className="text-lg  font-normal">Receipts Details</h2>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="receipt_date">
                  Receipt date:
                </Label>
                <Controller
                  name="receipt_date"
                  control={control}
                  render={({ field }) => (
                    <input
                      {...field}
                      id="receipt_date"
                      className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1 bg-gray-100"
                      type="date"
                      readOnly
                      placeholder="Enter receipt date"
                    />
                  )}
                />
                {errors.receipt_date && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.receipt_date.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="receipt_head">
                  Receipt Head: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="receipt_head"
                  control={control}
                  render={({ field }) => (
                    <Select value={field.value} onValueChange={field.onChange}>
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select receipt head" />
                      </SelectTrigger>
                      <SelectContent className="pb-10">
                        <SelectGroup>
                          <SelectLabel>Select receipt head</SelectLabel>
                          {allReceiptHeadsData?.ReceiptHeads &&
                            Object.keys(allReceiptHeadsData?.ReceiptHeads).map(
                              (key) => (
                                <SelectItem key={key} value={key}>
                                  {allReceiptHeadsData.ReceiptHeads[key]}
                                </SelectItem>
                              )
                            )}
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  )}
                />
                {errors.receipt_head && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.receipt_head.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="receipt_type_id">
                  Receipt Type: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="receipt_type_id"
                  control={control}
                  render={({ field }) => (
                    <Select value={field.value} onValueChange={field.onChange}>
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select receipt type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectLabel>Select receipt type</SelectLabel>
                          {allReceiptTypesData?.ReceiptTypes &&
                            allReceiptTypesData?.ReceiptTypes.map(
                              (ReceiptType) => (
                                <SelectItem value={String(ReceiptType.id)}>
                                  {ReceiptType.receipt_type}
                                </SelectItem>
                              )
                            )}
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  )}
                />
                {errors.receipt_type_id && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.receipt_type_id.message}
                  </p>
                )}
              </div>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative md:col-span-2">
                <Label className="font-normal" htmlFor="name">
                  Name: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="name"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="name"
                      className="mt-1"
                      type="text"
                      placeholder="Enter name"
                    />
                  )}
                />
                {errors.name && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.name.message}
                  </p>
                )}
              </div>
              <div className="relative ">
                <Label className="font-normal" htmlFor="gotra">
                  Gotra: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="gotra"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="gotra"
                      className="mt-1"
                      type="text"
                      placeholder="Enter gotra"
                    />
                  )}
                />
                {errors.gotra && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.gotra.message}
                  </p>
                )}
              </div>
            </div>

            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative md:col-start-3">
                <Label className="font-normal" htmlFor="amount">
                  Amount: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="amount"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="amount"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.amount && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.amount.message}
                  </p>
                )}
              </div>
            </div>

            {/* row ends */}
            <div className="w-full gap-4 mt-4 flex justify-end items-center">
              <Button
                type="button"
                className="dark:text-white shadow-xl bg-red-600 hover:bg-red-700"
                onClick={() => navigate("/receipts")}
              >
                Cancel
              </Button>

              {/* <Button
                type="submit"
                disabled={isLoading}
                className=" dark:text-white  shadow-xl bg-green-600 hover:bg-green-700"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="animate-spin mr-2" /> 
                    Submitting...
                  </>
                ) : (
                  "Submit"
                )}
              </Button> */}
            </div>
          </div>
        </form>
      </div>
    </>
  );
};

export default Update;

// <h1 style="text-align: center">श्री गणेश मंदिर संस्थान - नोट विवरण तख्ता {{ \Carbon\Carbon::parse($denomination->deposit_date)->format('d/m/Y') }}</h1>

// in indexheml i am writing this marahis words to print it on pdf but when pdf get generated then i am getting this
// marathis words
// श्री गणेश
// मंदरी संस् थान - नोट विवरण तख्ता 22/09/1999

// so what shold i do
// when i copy this from pdf and paste in google then it correclty display words but when printting it is not
