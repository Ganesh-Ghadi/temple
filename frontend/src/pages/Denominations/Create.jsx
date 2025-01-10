import React, { useState } from "react";
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
import { useNavigate } from "react-router-dom";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";

const formSchema = z.object({
  n_2000: z.coerce.number().optional(),
  n_500: z.coerce.number().optional(),
  n_200: z.coerce.number().optional(),
  n_100: z.coerce.number().optional(),
  n_50: z.coerce.number().optional(),
  n_20: z.coerce.number().optional(),
  n_10: z.coerce.number().optional(),
  c_20: z.coerce.number().optional(),
  c_10: z.coerce.number().optional(),
  c_5: z.coerce.number().optional(),
  c_2: z.coerce.number().optional(),
  c_1: z.coerce.number().optional(),
  amount: z.coerce.number().min(0.01, { message: "amount field is required" }),
  deposit_date: z.string().min(1, "deposit date field is required"),
});
const Create = () => {
  const [isLoading, setIsLoading] = useState(false);
  const queryClient = useQueryClient();
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;
  const navigate = useNavigate();
  const defaultValues = {
    n_2000: "",
    n_500: "",
    n_200: "",
    n_100: "",
    n_50: "",
    n_20: "",
    n_10: "",
    c_20: "",
    c_10: "",
    c_5: "",
    c_2: "",
    c_1: "",
    deposit_date: "",
    amount: "",
  };

  const {
    control,
    handleSubmit,
    formState: { errors },
    setError,
  } = useForm({ resolver: zodResolver(formSchema), defaultValues });

  const storeMutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.post("/api/denominations", data, {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`, // Include the Bearer token
        },
      });
      return response.data;
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries("denominations");
      toast.success("Denominations Added Successfully");
      setIsLoading(false);
      navigate("/denominations");
    },
    onError: (error) => {
      setIsLoading(false);

      console.log("got error ", error);
    },
  });
  const onSubmit = (data) => {
    setIsLoading(true);
    toast.error("Faild to update denomination");

    storeMutation.mutate(data);
  };

  return (
    <>
      <div className="p-5">
        {/* breadcrumb start */}
        <div className=" mb-7 text-sm">
          <div className="flex items-center space-x-2 text-gray-700">
            <span className="">
              <Button
                onClick={() => navigate("/denominations")}
                className="p-0 text-blue-700 text-sm font-light"
                variant="link"
              >
                Denominations
              </Button>
            </span>
            <span className="text-gray-400">/</span>
            <span className="dark:text-gray-300">Add</span>
          </div>
        </div>
        {/* breadcrumb ends */}

        {/* form style strat */}
        <form onSubmit={handleSubmit(onSubmit)}>
          <div className="px-5 pb-4 mb-5 dark:bg-gray-800 pt-1 w-full bg-white shadow-lg border  rounded-md">
            <div className="w-full py-3 flex justify-start items-center">
              <h2 className="text-lg  font-normal">Deposit Details</h2>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="deposit_date">
                  Deposit date:<span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="deposit_date"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="deposit_date"
                      className="mt-1"
                      type="date"
                      placeholder="Enter date"
                    />
                  )}
                />
                {errors.deposit_date && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.deposit_date.message}
                  </p>
                )}
              </div>
            </div>
          </div>

          <div className="px-5 pb-7 mb-5 dark:bg-gray-800 pt-1 w-full bg-white shadow-lg border  rounded-md">
            <div className="w-full py-3 flex justify-start items-center">
              <h2 className="text-lg  font-normal">Notes Denominations</h2>
            </div>
            {/* row starts */}
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="pooja_type">
                  2000 x:
                </Label>
                <Controller
                  name="n_2000"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="n_2000"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.n_2000 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.n_2000.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="n_500">
                  500 x:
                </Label>
                <Controller
                  name="n_500"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="n_500"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.n_500 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.n_500.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="n_200">
                  200 x:
                </Label>
                <Controller
                  name="n_200"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="n_200"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.n_200 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.n_200.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="n_100">
                  100 x:
                </Label>
                <Controller
                  name="n_100"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="n_100"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.n_100 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.n_100.message}
                  </p>
                )}
              </div>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="n_50">
                  50 x:
                </Label>
                <Controller
                  name="n_50"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="n_50"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.n_50 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.n_50.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="n_20">
                  20 x:
                </Label>
                <Controller
                  name="n_20"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="n_20"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.n_20 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.n_20.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="n_10">
                  10 x:
                </Label>
                <Controller
                  name="n_10"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="n_10"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.n_10 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.n_10.message}
                  </p>
                )}
              </div>
            </div>
            {/* row ends */}
          </div>

          <div className="px-5 pb-7 dark:bg-gray-800 pt-1 w-full bg-white shadow-lg border  rounded-md">
            <div className="w-full py-3 flex justify-start items-center">
              <h2 className="text-lg  font-normal">Coins Denominations</h2>
            </div>
            {/* row starts */}
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="c_20">
                  20 x:
                </Label>
                <Controller
                  name="c_20"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="c_20"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.c_20 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.c_20.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="c_10">
                  10 x:
                </Label>
                <Controller
                  name="c_10"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="c_10"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.c_10 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.c_10.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="c_5">
                  5 x:
                </Label>
                <Controller
                  name="c_5"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="c_5"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.c_5 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.c_5.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="c_2">
                  2 x:
                </Label>
                <Controller
                  name="c_2"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="c_2"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.c_2 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.c_2.message}
                  </p>
                )}
              </div>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="c_1">
                  1 x:
                </Label>
                <Controller
                  name="c_1"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="c_1"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.c_1 && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.c_1.message}
                  </p>
                )}
              </div>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 border-t-2 dark:border-gray-600 pt-3 md:grid-cols-4 gap-7 md:gap-4">
              <div className="relative  md:col-start-4">
                <Label className="font-normal" htmlFor="amount">
                  Total Amount:<span className="text-red-500">*</span>
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
                className="dark:text-white shadow-xl bg-red-500 hover:bg-red-600"
                onClick={() => navigate("/denominations")}
              >
                Cancle
              </Button>

              <Button
                type="submit"
                disabled={isLoading}
                className=" dark:text-white  shadow-xl bg-green-500 hover:bg-green-600"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="animate-spin mr-2" /> {/* Spinner */}
                    Submitting...
                  </>
                ) : (
                  "Submit"
                )}
              </Button>
            </div>
          </div>
        </form>
      </div>
    </>
  );
};

export default Create;
